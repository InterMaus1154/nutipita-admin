<?php

namespace Tests\Feature\Domain\Invoice\Services;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Enums\InvoiceStatus;
use App\Domain\Invoice\Exceptions\CannotUpdateInvoiceStatusException;
use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;
use App\Domain\Invoice\Services\InvoiceService;
use App\Enums\OrderStatus;
use App\Infrastructure\Invoice\InvoiceEloquentRepository;
use App\Models\CreditNote;
use Illuminate\Support\Facades\DB;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Storage;
use App\Domain\Invoice\Data\CreateInvoiceData;
use App\Domain\Invoice\Data\InvoiceProductData;
use App\Domain\Invoice\Entities\DeliveryCharge;
use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Enums\CreateInvoiceMode;
use App\Domain\Invoice\Exceptions\CannotCreateInvoiceException;
use App\Domain\Invoice\Exceptions\NoProductLineException;
use App\Domain\Invoice\Strategies\AbstractCreateInvoiceStrategy;
use App\Domain\Invoice\Strategies\CreateInvoiceStrategyFactory;
use App\Domain\Order\Exceptions\OrderAlreadyInvoicedException;
use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Tests\TestCase;
use Tests\TestHelpers;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    private InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('invoices');
        $this->service = $this->app->make(InvoiceService::class);
    }

    // ====
    // delete
    // ====

    public function test_delete_removes_the_invoice_and_its_pivot_rows(): void
    {
        $invoice = $this->createInvoice();
        $order = $this->createOrder($invoice->customer_id);
        $invoice->orders()->attach($order->order_id);

        $this->service->delete($invoice->invoice_id);

        $this->assertDatabaseMissing('invoices', ['invoice_id' => $invoice->invoice_id]);
        $this->assertDatabaseMissing('invoice_orders', ['invoice_id' => $invoice->invoice_id]);
    }

    public function test_delete_sets_every_order_of_the_invoice_back_to_confirmed(): void
    {
        $invoice = $this->createInvoice();
        $paid = $this->createOrder($invoice->customer_id, OrderStatus::G_PAID);
        $unpaid = $this->createOrder($invoice->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $invoice->orders()->attach([$paid->order_id, $unpaid->order_id]);

        $this->service->delete($invoice->invoice_id);

        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($paid));
        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($unpaid));
    }

    public function test_delete_keeps_the_orders_themselves(): void
    {
        $invoice = $this->createInvoice();
        $order = $this->createOrder($invoice->customer_id);
        $invoice->orders()->attach($order->order_id);

        $this->service->delete($invoice->invoice_id);

        $this->assertDatabaseHas('orders', ['order_id' => $order->order_id]);
    }

    public function test_delete_does_not_touch_orders_that_are_not_on_the_invoice(): void
    {
        $invoice = $this->createInvoice();
        $onInvoice = $this->createOrder($invoice->customer_id, OrderStatus::G_PAID);
        $sameCustomerOther = $this->createOrder($invoice->customer_id, OrderStatus::G_PAID);
        $otherCustomer = $this->createOrder(status: OrderStatus::O_DELIVERED_UNPAID);
        $invoice->orders()->attach($onInvoice->order_id);

        $this->service->delete($invoice->invoice_id);

        $this->assertSame('G_PAID', $this->storedOrderStatus($sameCustomerOther));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($otherCustomer));
    }

    public function test_delete_does_not_touch_other_invoices_or_their_orders(): void
    {
        $gone = $this->createInvoice();
        $goneOrder = $this->createOrder($gone->customer_id);
        $gone->orders()->attach($goneOrder->order_id);

        $kept = $this->createInvoice();
        $keptOrder = $this->createOrder($kept->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $kept->orders()->attach($keptOrder->order_id);

        $this->service->delete($gone->invoice_id);

        $this->assertDatabaseHas('invoices', ['invoice_id' => $kept->invoice_id]);
        $this->assertDatabaseHas('invoice_orders', ['invoice_id' => $kept->invoice_id, 'order_id' => $keptOrder->order_id]);
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($keptOrder));
    }

    public function test_delete_removes_the_pdf_file(): void
    {
        $invoice = $this->createInvoice();
        Storage::disk('local')->put($invoice->invoice_path, 'pdf');

        $this->service->delete($invoice->invoice_id);

        Storage::disk('local')->assertMissing($invoice->invoice_path);
    }

    public function test_delete_does_not_remove_the_pdf_of_other_invoices(): void
    {
        $gone = $this->createInvoice();
        $kept = $this->createInvoice();
        Storage::disk('local')->put($gone->invoice_path, 'pdf');
        Storage::disk('local')->put('invoices/other.pdf', 'pdf');
        $kept->update(['invoice_path' => 'invoices/other.pdf']);

        $this->service->delete($gone->invoice_id);

        Storage::disk('local')->assertExists('invoices/other.pdf');
    }

    public function test_delete_works_for_an_invoice_without_orders_or_a_pdf_file(): void
    {
        $invoice = $this->createInvoice();

        $this->service->delete($invoice->invoice_id);

        $this->assertDatabaseMissing('invoices', ['invoice_id' => $invoice->invoice_id]);
    }

    public function test_delete_throws_invoice_not_found_for_an_unknown_id(): void
    {
        $this->expectException(InvoiceNotFoundException::class);
        $this->expectExceptionMessage('999999');

        $this->service->delete(999999);
    }

    public function test_delete_of_an_unknown_id_changes_nothing(): void
    {
        $invoice = $this->createInvoice();
        $order = $this->createOrder($invoice->customer_id, OrderStatus::G_PAID);
        $invoice->orders()->attach($order->order_id);

        try {
            $this->service->delete(999999);
            $this->fail('Expected InvoiceNotFoundException');
        } catch (InvoiceNotFoundException) {
        }

        $this->assertDatabaseHas('invoices', ['invoice_id' => $invoice->invoice_id]);
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }

    public function test_delete_reports_the_exception_it_throws(): void
    {
        Exceptions::fake();

        try {
            $this->service->delete(999999);
        } catch (InvoiceNotFoundException) {
        }

        Exceptions::assertReported(InvoiceNotFoundException::class);
    }

    public function test_delete_rolls_everything_back_when_the_invoice_cannot_be_deleted(): void
    {
        // credit_notes.invoice_id has no cascade, so the database refuses to delete an invoice that has one
        $invoice = $this->createInvoice();
        $order = $this->createOrder($invoice->customer_id, OrderStatus::G_PAID);
        $invoice->orders()->attach($order->order_id);
        Storage::disk('local')->put($invoice->invoice_path, 'pdf');
        CreditNote::create([
            'invoice_id' => $invoice->invoice_id,
            'credit_note_issued_at' => '2026-03-10',
            'credit_note_number' => 'CN-0001',
        ]);

        try {
            $this->service->delete($invoice->invoice_id);
            $this->fail('Expected the database to refuse the delete');
        } catch (QueryException) {
        }

        $this->assertDatabaseHas('invoices', ['invoice_id' => $invoice->invoice_id]);
        $this->assertDatabaseHas('invoice_orders', ['invoice_id' => $invoice->invoice_id, 'order_id' => $order->order_id]);
        $this->assertSame('G_PAID', $this->storedOrderStatus($order), 'the order status update must be rolled back');
        Storage::disk('local')->assertExists($invoice->invoice_path);
    }

    // ====
    // updateStatus
    // ====

    private function storedInvoiceStatus(int $invoiceId): int
    {
        return (int)DB::table('invoices')->where('invoice_id', $invoiceId)->value('invoice_status_new');
    }

    /**
     * @return array<string, array{InvoiceStatus, OrderStatus}>
     */
    public static function statusToOrderStatusProvider(): array
    {
        return [
            'paid' => [InvoiceStatus::PAID, OrderStatus::G_PAID],
            'unpaid' => [InvoiceStatus::UNPAID, OrderStatus::O_DELIVERED_UNPAID],
            'loss' => [InvoiceStatus::LOSS, OrderStatus::O_DELIVERED_UNPAID],
            'cancelled' => [InvoiceStatus::CANCELLED, OrderStatus::Y_CONFIRMED],
        ];
    }

    #[DataProvider('statusToOrderStatusProvider')]
    public function test_update_status_updates_the_invoice_and_maps_the_order_status(InvoiceStatus $status, OrderStatus $expectedOrderStatus): void
    {
        // start from a status different from the expected one so every case is a real change
        $startStatus = $expectedOrderStatus === OrderStatus::G_PAID ? OrderStatus::Y_CONFIRMED : OrderStatus::G_PAID;
        $invoice = $this->createInvoice($status === InvoiceStatus::PAID ? InvoiceStatus::UNPAID : InvoiceStatus::PAID);
        $first = $this->createOrder($invoice->customer_id, $startStatus);
        $second = $this->createOrder($invoice->customer_id, $startStatus);
        $invoice->orders()->attach([$first->order_id, $second->order_id]);

        $this->service->updateStatus($invoice->invoice_id, $status);

        $this->assertSame($status->value, $this->storedInvoiceStatus($invoice->invoice_id));
        $this->assertSame($expectedOrderStatus->name, $this->storedOrderStatus($first));
        $this->assertSame($expectedOrderStatus->name, $this->storedOrderStatus($second));
    }

    public function test_update_status_does_not_touch_orders_that_are_not_on_the_invoice(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);
        $onInvoice = $this->createOrder($invoice->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $sameCustomerOther = $this->createOrder($invoice->customer_id, OrderStatus::Y_CONFIRMED);
        $otherCustomer = $this->createOrder(status: OrderStatus::Y_CONFIRMED);
        $invoice->orders()->attach($onInvoice->order_id);

        $this->service->updateStatus($invoice->invoice_id, InvoiceStatus::PAID);

        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($sameCustomerOther));
        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($otherCustomer));
    }

    public function test_update_status_does_not_touch_other_invoices(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);
        $other = $this->createInvoice(InvoiceStatus::UNPAID);
        $otherOrder = $this->createOrder($other->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $other->orders()->attach($otherOrder->order_id);

        $this->service->updateStatus($invoice->invoice_id, InvoiceStatus::PAID);

        $this->assertSame(InvoiceStatus::UNPAID->value, $this->storedInvoiceStatus($other->invoice_id));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($otherOrder));
    }

    public function test_update_status_works_for_an_invoice_without_orders(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);

        $this->service->updateStatus($invoice->invoice_id, InvoiceStatus::PAID);

        $this->assertSame(InvoiceStatus::PAID->value, $this->storedInvoiceStatus($invoice->invoice_id));
    }

    public function test_update_status_throws_cannot_update_for_an_unknown_id_and_keeps_the_cause(): void
    {
        try {
            $this->service->updateStatus(999999, InvoiceStatus::PAID);
            $this->fail('Expected CannotUpdateInvoiceStatusException');
        } catch (CannotUpdateInvoiceStatusException $e) {
            $this->assertStringContainsString('999999', $e->getMessage());
            $this->assertInstanceOf(InvoiceNotFoundException::class, $e->getPrevious());
        }
    }

    public function test_update_status_of_an_unknown_id_changes_nothing(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);
        $order = $this->createOrder($invoice->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $invoice->orders()->attach($order->order_id);

        try {
            $this->service->updateStatus(999999, InvoiceStatus::PAID);
        } catch (CannotUpdateInvoiceStatusException) {
        }

        $this->assertSame(InvoiceStatus::UNPAID->value, $this->storedInvoiceStatus($invoice->invoice_id));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($order));
    }

    public function test_update_status_rolls_back_the_order_update_when_saving_the_invoice_fails(): void
    {
        $invoice = $this->createInvoice(InvoiceStatus::UNPAID);
        $order = $this->createOrder($invoice->customer_id, OrderStatus::O_DELIVERED_UNPAID);
        $invoice->orders()->attach($order->order_id);

        // orders are updated first, then the commit of the invoice blows up
        $failing = Mockery::mock(InvoiceEloquentRepository::class)->makePartial();
        $failing->shouldReceive('commit')->once()->andThrow(new \RuntimeException('boom'));
        $this->app->instance(InvoiceRepository::class, $failing);
        $levelBefore = DB::transactionLevel();
        $service = $this->app->make(InvoiceService::class);

        try {
            $service->updateStatus($invoice->invoice_id, InvoiceStatus::PAID);
            $this->fail('Expected CannotUpdateInvoiceStatusException');
        } catch (CannotUpdateInvoiceStatusException $e) {
            $this->assertSame('boom', $e->getPrevious()->getMessage());
        }

        $this->assertSame(InvoiceStatus::UNPAID->value, $this->storedInvoiceStatus($invoice->invoice_id));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($order), 'the order status update must be rolled back');
        $this->assertSame($levelBefore, DB::transactionLevel(), 'the transaction must be closed');
    }

    // ====
    // createRecordUsingStrategy
    // ====

    private function singleStrategy(int $orderId): AbstractCreateInvoiceStrategy
    {
        return $this->app->make(CreateInvoiceStrategyFactory::class)->single($orderId);
    }

    private function addLine(Order $order, Product $product, int $qty, float $unitPrice): void
    {
        $order->products()->attach($product->product_id, [
            'product_qty' => $qty,
            'order_product_unit_price' => $unitPrice,
        ]);
    }

    /**
     * An order with one line of 2 x 1.50
     */
    private function orderWithLines(?Customer $customer = null, OrderStatus $status = OrderStatus::G_PAID, string $dueDate = '2026-03-02'): Order
    {
        $order = $this->createOrder($customer, $status, $dueDate);
        $this->addLine($order, $this->createProduct(), 2, 1.50);

        return $order;
    }

    /**
     * A strategy that hands the service exactly the data given, for inputs the single strategy can't produce
     *
     * @param InvoiceProductData[] $products
     * @param int[] $orderIds
     */
    private function manualStrategy(
        int             $customerId,
        array           $products,
        array           $orderIds = [],
        ?DeliveryCharge $charge = null,
        string          $number = '0100',
        ?Carbon         $from = null,
        ?Carbon         $to = null,
    ): AbstractCreateInvoiceStrategy
    {
        $data = new CreateInvoiceData($customerId, Carbon::now(), Carbon::now()->addDay(), $number, $charge, $products, $orderIds, $from, $to);

        return new class($data) extends AbstractCreateInvoiceStrategy {
            public function __construct(private CreateInvoiceData $data)
            {
            }

            public function mode(): CreateInvoiceMode
            {
                return CreateInvoiceMode::MANUAL;
            }

            public function makeCreateInvoiceData(): CreateInvoiceData
            {
                return $this->data;
            }
        };
    }

    private function createAndCatch(AbstractCreateInvoiceStrategy $strategy): CannotCreateInvoiceException
    {
        try {
            $this->service->createRecordUsingStrategy($strategy);
        } catch (CannotCreateInvoiceException $e) {
            return $e;
        }

        $this->fail('Expected CannotCreateInvoiceException');
    }

    /**
     * @return array{int, int, int} invoices, pivot rows and product lines in the database
     */
    private function writtenRowCounts(): array
    {
        return [
            DB::table('invoices')->count(),
            DB::table('invoice_orders')->count(),
            DB::table('invoice_products')->count(),
        ];
    }

    private function storedInvoice(int $invoiceId): object
    {
        return DB::table('invoices')->where('invoice_id', $invoiceId)->first();
    }

    /**
     * Swaps the logger for one that records what it is given, and rebuilds the service with it
     */
    private function recordLogs(bool $failing = false): object
    {
        $logger = new class($failing) extends AbstractLogger {
            /** @var array<int, array{level: string, message: string, context: array}> */
            public array $records = [];

            public function __construct(private bool $failing)
            {
            }

            public function log($level, \Stringable|string $message, array $context = []): void
            {
                if ($this->failing) {
                    throw new \RuntimeException('the log cannot be written');
                }
                $this->records[] = ['level' => $level, 'message' => (string)$message, 'context' => $context];
            }
        };

        $this->app->instance(LoggerInterface::class, $logger);
        $this->service = $this->app->make(InvoiceService::class);

        return $logger;
    }

    // ---- happy path

    public function test_create_returns_the_persisted_invoice(): void
    {
        $order = $this->orderWithLines();

        $invoice = $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertTrue($invoice->exists);
        $this->assertNotNull($this->storedInvoice($invoice->invoice_id));
    }

    public function test_create_saves_the_invoice_fields(): void
    {
        $this->travelTo(Carbon::parse('2026-05-10 12:00:00'));
        $customer = $this->createCustomer();
        $order = $this->orderWithLines($customer, dueDate: '2026-03-02');

        $invoice = $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $row = $this->storedInvoice($invoice->invoice_id);
        $this->assertSame($customer->customer_id, $row->customer_id);
        $this->assertSame('0001', $row->invoice_number);
        $this->assertSame(InvoiceStatus::UNPAID->value, $row->invoice_status_new);
        $this->assertSame('2026-05-10', $row->invoice_issue_date);
        $this->assertSame('2026-05-11', $row->invoice_due_date);
        $this->assertSame('2026-03-02', $row->invoice_from);
        $this->assertSame('2026-03-02', $row->invoice_to);
        $this->assertSame('INV-0001.pdf', $row->invoice_name);
        $this->assertSame('invoices/INV-0001.pdf', $row->invoice_path);
        $this->assertNull($row->invoice_delivery_charge);
        $this->assertNull($row->order_id, 'the legacy single order column must not be used any more');
    }

    public function test_create_total_is_the_sum_of_quantity_times_price(): void
    {
        $order = $this->createOrder();
        $this->addLine($order, $this->createProduct(), 2, 1.50);
        $this->addLine($order, $this->createProduct(), 10, 0.35);

        $invoice = $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $this->assertEqualsWithDelta(6.50, (float)$this->storedInvoice($invoice->invoice_id)->invoice_total, 0.001);
    }

    public function test_create_links_the_invoice_to_the_order_through_the_pivot(): void
    {
        $order = $this->orderWithLines();

        $invoice = $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $this->assertSame(
            [['invoice_id' => $invoice->invoice_id, 'order_id' => $order->order_id]],
            DB::table('invoice_orders')->get(['invoice_id', 'order_id'])->map(fn($r) => (array)$r)->all()
        );
    }

    public function test_create_saves_the_product_lines_with_the_order_prices(): void
    {
        $order = $this->createOrder();
        $bread = $this->createProduct();
        $roll = $this->createProduct();
        $this->addLine($order, $bread, 10, 0.75);
        $this->addLine($order, $roll, 3, 2.20);

        $invoice = $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $lines = DB::table('invoice_products')->where('invoice_id', $invoice->invoice_id)->get()->keyBy('product_id');
        $this->assertCount(2, $lines);
        $this->assertSame(10, $lines[$bread->product_id]->product_qty);
        $this->assertEqualsWithDelta(0.75, (float)$lines[$bread->product_id]->product_unit_price, 0.0001);
        $this->assertSame(3, $lines[$roll->product_id]->product_qty);
        $this->assertEqualsWithDelta(2.20, (float)$lines[$roll->product_id]->product_unit_price, 0.0001);
    }

    /**
     * @return array<string, array{OrderStatus}>
     */
    public static function orderStatusesBeforeInvoicing(): array
    {
        $sets = [];
        foreach (OrderStatus::cases() as $status) {
            $sets[$status->name] = [$status];
        }
        return $sets;
    }

    #[DataProvider('orderStatusesBeforeInvoicing')]
    public function test_create_sets_the_order_to_delivered_unpaid(OrderStatus $before): void
    {
        $order = $this->orderWithLines(status: $before);

        $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($order));
    }

    public function test_create_does_not_touch_other_orders(): void
    {
        $customer = $this->createCustomer();
        $order = $this->orderWithLines($customer);
        $sameCustomer = $this->createOrder($customer, OrderStatus::G_PAID);
        $otherCustomer = $this->createOrder(status: OrderStatus::Y_CONFIRMED);

        $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $this->assertSame('G_PAID', $this->storedOrderStatus($sameCustomer));
        $this->assertSame('Y_CONFIRMED', $this->storedOrderStatus($otherCustomer));
    }

    public function test_create_commits_the_transaction(): void
    {
        $order = $this->orderWithLines();
        $levelBefore = DB::transactionLevel();

        $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $this->assertSame($levelBefore, DB::transactionLevel());
    }

    public function test_create_numbers_invoices_sequentially(): void
    {
        $customer = $this->createCustomer();
        $first = $this->orderWithLines($customer);
        $second = $this->orderWithLines($customer);

        $a = $this->service->createRecordUsingStrategy($this->singleStrategy($first->order_id));
        $b = $this->service->createRecordUsingStrategy($this->singleStrategy($second->order_id));

        $this->assertSame('0001', $a->invoice_number);
        $this->assertSame('0002', $b->invoice_number);
    }

    public function test_create_only_logs_info_messages_on_success(): void
    {
        $logger = $this->recordLogs();
        $order = $this->orderWithLines();

        $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        // the whole chain runs: InvoiceCreated, the pdf is saved, FileSaved. Only info lines are logged, no warnings, no errors
        $levels = array_unique(array_column($logger->records, 'level'));
        $this->assertSame(['info'], array_values($levels));

        $messages = implode("\n", array_column($logger->records, 'message'));
        $this->assertStringContainsString('InvoiceCreated Dispatched', $messages);
        $this->assertStringContainsString('FileSaved Dispatched', $messages);
        $this->assertStringContainsString('New file saved to invoices/INV-0001.pdf', $messages);
    }

    public function test_a_created_invoice_can_be_loaded_with_everything_the_pdf_needs(): void
    {
        $order = $this->orderWithLines();
        $invoice = $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $loaded = $this->app->make(InvoiceRepository::class)->findById($invoice->invoice_id);

        $this->assertSame($order->customer_id, $loaded->customer->customer_id);
        $this->assertSame([$order->order_id], $loaded->orders->pluck('order_id')->all());
        $this->assertCount(1, $loaded->products);
        $this->assertNotNull($loaded->products->first()->product->product_name);
    }

    // ---- edge cases

    /**
     * @return array<string, array{string, float, string}>
     */
    public static function deliveryCharges(): array
    {
        return [
            'flat charge' => ['5', 5.0, '5'],
            'charge with a multiplier' => ['2x3.5', 7.0, '2x3.5'],
            'decimal charge' => ['4.25', 4.25, '4.25'],
        ];
    }

    #[DataProvider('deliveryCharges')]
    public function test_create_adds_the_delivery_charge_to_the_total(string $input, float $expectedCharge, string $expectedStored): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct();

        $invoice = $this->service->createRecordUsingStrategy($this->manualStrategy(
            $customer->customer_id,
            [new InvoiceProductData($product->product_id, 4, 2.50)], // 10.00
            charge: DeliveryCharge::fromString($input),
        ));

        $row = $this->storedInvoice($invoice->invoice_id);
        $this->assertEqualsWithDelta(10.00 + $expectedCharge, (float)$row->invoice_total, 0.001);
        $this->assertSame($expectedStored, $row->invoice_delivery_charge);
    }

    public function test_create_without_a_delivery_charge_adds_nothing(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct();

        $invoice = $this->service->createRecordUsingStrategy($this->manualStrategy(
            $customer->customer_id,
            [new InvoiceProductData($product->product_id, 4, 2.50)],
        ));

        $row = $this->storedInvoice($invoice->invoice_id);
        $this->assertEqualsWithDelta(10.00, (float)$row->invoice_total, 0.001);
        $this->assertNull($row->invoice_delivery_charge);
    }

    public function test_create_can_link_several_orders_to_one_invoice(): void
    {
        $customer = $this->createCustomer();
        $a = $this->createOrder($customer, OrderStatus::G_PAID);
        $b = $this->createOrder($customer, OrderStatus::Y_CONFIRMED);
        $untouched = $this->createOrder($customer, OrderStatus::G_PAID);
        $product = $this->createProduct();

        $invoice = $this->service->createRecordUsingStrategy($this->manualStrategy(
            $customer->customer_id,
            [new InvoiceProductData($product->product_id, 1, 1.00)],
            orderIds: [$a->order_id, $b->order_id],
        ));

        $linked = DB::table('invoice_orders')->where('invoice_id', $invoice->invoice_id)->pluck('order_id')->all();
        $this->assertEqualsCanonicalizing([$a->order_id, $b->order_id], $linked);
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($a));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($b));
        $this->assertSame('G_PAID', $this->storedOrderStatus($untouched));
    }

    public function test_create_without_any_order_saves_the_invoice_and_touches_no_orders(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, OrderStatus::G_PAID);
        $product = $this->createProduct();

        $invoice = $this->service->createRecordUsingStrategy($this->manualStrategy(
            $customer->customer_id,
            [new InvoiceProductData($product->product_id, 1, 1.00)],
        ));

        $this->assertNotNull($this->storedInvoice($invoice->invoice_id));
        $this->assertDatabaseCount('invoice_orders', 0);
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }

    public function test_create_without_a_period_stores_null_dates(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct();

        $invoice = $this->service->createRecordUsingStrategy($this->manualStrategy(
            $customer->customer_id,
            [new InvoiceProductData($product->product_id, 1, 1.00)],
        ));

        $row = $this->storedInvoice($invoice->invoice_id);
        $this->assertNull($row->invoice_from);
        $this->assertNull($row->invoice_to);
    }

    public function test_create_accepts_a_zero_quantity_line(): void
    {
        $order = $this->createOrder();
        $this->addLine($order, $this->createProduct(), 0, 1.00);

        $invoice = $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $this->assertEqualsWithDelta(0.0, (float)$this->storedInvoice($invoice->invoice_id)->invoice_total, 0.001);
        $this->assertDatabaseCount('invoice_products', 1);
    }

    public function test_create_does_not_lose_cents_to_floating_point(): void
    {
        $order = $this->createOrder();
        $this->addLine($order, $this->createProduct(), 75, 0.35); // 26.25, but 75 * 0.35 is not exact in a float

        $invoice = $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $this->assertSame('26.25', $this->storedInvoice($invoice->invoice_id)->invoice_total);
    }

    public function test_create_keeps_four_decimals_on_the_unit_price(): void
    {
        $order = $this->createOrder();
        $product = $this->createProduct();
        $this->addLine($order, $product, 3, 0.3333);

        $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $this->assertSame('0.3333', DB::table('invoice_products')->where('product_id', $product->product_id)->value('product_unit_price'));
    }

    // ---- failure path

    public function test_create_fails_for_an_unknown_order_with_a_readable_message(): void
    {
        $before = $this->writtenRowCounts();

        $e = $this->createAndCatch($this->singleStrategy(999999));

        $this->assertSame('There was an error generating invoice. Order with id #999999 not found', $e->getMessage());
        $this->assertInstanceOf(OrderNotFoundException::class, $e->getPrevious());
        $this->assertSame($before, $this->writtenRowCounts());
    }

    public function test_create_fails_for_an_order_that_is_already_invoiced(): void
    {
        $order = $this->orderWithLines();
        $existing = $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));
        $before = $this->writtenRowCounts();

        $e = $this->createAndCatch($this->singleStrategy($order->order_id));

        $this->assertStringContainsString('There was an error generating invoice.', $e->getMessage());
        $this->assertStringContainsString($existing->invoice_number, $e->getMessage());
        $this->assertInstanceOf(OrderAlreadyInvoicedException::class, $e->getPrevious());
        $this->assertSame($before, $this->writtenRowCounts());
        $this->assertDatabaseHas('invoices', ['invoice_id' => $existing->invoice_id]);
    }

    public function test_create_fails_for_an_order_linked_through_the_legacy_order_id(): void
    {
        $order = $this->orderWithLines();
        $invoice = $this->createInvoice(customer: $order->customer);
        $invoice->update(['order_id' => $order->order_id]);
        $before = $this->writtenRowCounts();

        $e = $this->createAndCatch($this->singleStrategy($order->order_id));

        $this->assertInstanceOf(OrderAlreadyInvoicedException::class, $e->getPrevious());
        $this->assertStringContainsString($invoice->invoice_number, $e->getMessage());
        $this->assertSame($before, $this->writtenRowCounts());
    }

    public function test_create_fails_for_an_order_without_products_and_keeps_its_status(): void
    {
        $order = $this->createOrder(status: OrderStatus::G_PAID);
        $before = $this->writtenRowCounts();

        $e = $this->createAndCatch($this->singleStrategy($order->order_id));

        $this->assertSame('There was an error generating invoice. Invoice cannot be created without products', $e->getMessage());
        $this->assertInstanceOf(NoProductLineException::class, $e->getPrevious());
        $this->assertSame($before, $this->writtenRowCounts());
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }

    public function test_create_hides_unexpected_errors_behind_a_generic_message(): void
    {
        $customer = $this->createCustomer();

        // the product does not exist, so the database refuses the product line
        $e = $this->createAndCatch($this->manualStrategy(
            $customer->customer_id,
            [new InvoiceProductData(987654, 1, 1.00)],
        ));

        $this->assertSame('There was an error generating invoice.', $e->getMessage());
        $this->assertInstanceOf(QueryException::class, $e->getPrevious());
        $this->assertStringNotContainsString('SQLSTATE', $e->getMessage());
    }

    public function test_create_rolls_back_everything_when_a_later_write_fails(): void
    {
        // the invoice row and the pivot row are written before the product line fails
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, OrderStatus::G_PAID);
        $before = $this->writtenRowCounts();
        $levelBefore = DB::transactionLevel();

        $this->createAndCatch($this->manualStrategy(
            $customer->customer_id,
            [new InvoiceProductData(987654, 1, 1.00)],
            orderIds: [$order->order_id],
        ));

        $this->assertSame($before, $this->writtenRowCounts());
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
        $this->assertSame($levelBefore, DB::transactionLevel(), 'the transaction must be closed');
    }

    public function test_create_rolls_back_when_the_pivot_write_fails(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct();
        $before = $this->writtenRowCounts();

        $e = $this->createAndCatch($this->manualStrategy(
            $customer->customer_id,
            [new InvoiceProductData($product->product_id, 1, 1.00)],
            orderIds: [999999], // no such order
        ));

        $this->assertInstanceOf(QueryException::class, $e->getPrevious());
        $this->assertSame($before, $this->writtenRowCounts());
    }

    public function test_create_rolls_back_when_the_invoice_number_is_already_taken(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, OrderStatus::G_PAID);
        $product = $this->createProduct();
        $taken = $this->createInvoice(customer: $customer);
        $before = $this->writtenRowCounts();

        $e = $this->createAndCatch($this->manualStrategy(
            $customer->customer_id,
            [new InvoiceProductData($product->product_id, 1, 1.00)],
            orderIds: [$order->order_id],
            number: $taken->invoice_number,
        ));

        $this->assertSame('There was an error generating invoice.', $e->getMessage());
        $this->assertSame($before, $this->writtenRowCounts());
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }

    public function test_create_rolls_back_when_the_same_product_is_on_two_lines(): void
    {
        // invoice_products has one row per product and invoice
        $customer = $this->createCustomer();
        $product = $this->createProduct();
        $before = $this->writtenRowCounts();

        $this->createAndCatch($this->manualStrategy($customer->customer_id, [
            new InvoiceProductData($product->product_id, 1, 1.00),
            new InvoiceProductData($product->product_id, 2, 1.00),
        ]));

        $this->assertSame($before, $this->writtenRowCounts());
    }

    public function test_a_failed_attempt_does_not_use_up_the_invoice_number(): void
    {
        $this->createAndCatch($this->singleStrategy(999999));
        $order = $this->orderWithLines();

        $invoice = $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $this->assertSame('0001', $invoice->invoice_number);
    }

    public function test_creating_twice_for_the_same_order_creates_one_invoice(): void
    {
        $order = $this->orderWithLines();
        $this->service->createRecordUsingStrategy($this->singleStrategy($order->order_id));

        $this->createAndCatch($this->singleStrategy($order->order_id));

        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseCount('invoice_orders', 1);
    }

    public function test_every_failure_leaves_the_transaction_closed(): void
    {
        $customer = $this->createCustomer();
        $empty = $this->createOrder($customer);
        $levelBefore = DB::transactionLevel();

        $this->createAndCatch($this->singleStrategy(999999));
        $this->createAndCatch($this->singleStrategy($empty->order_id));
        $this->createAndCatch($this->manualStrategy($customer->customer_id, [new InvoiceProductData(987654, 1, 1.00)]));

        $this->assertSame($levelBefore, DB::transactionLevel());
    }

    // ---- logging

    public function test_expected_failures_are_logged_at_info_with_the_exception_and_mode(): void
    {
        $logger = $this->recordLogs();

        $this->createAndCatch($this->singleStrategy(999999));

        $this->assertCount(1, $logger->records);
        $record = $logger->records[0];
        $this->assertSame('info', $record['level']);
        $this->assertInstanceOf(OrderNotFoundException::class, $record['context']['exception']);
        $this->assertSame(CreateInvoiceMode::SINGLE, $record['context']['mode']);
        $this->assertStringContainsString('single', $record['message']);
    }

    public function test_unexpected_failures_are_logged_at_error_with_the_exception_and_mode(): void
    {
        $logger = $this->recordLogs();
        $customer = $this->createCustomer();

        $this->createAndCatch($this->manualStrategy($customer->customer_id, [new InvoiceProductData(987654, 1, 1.00)]));

        $this->assertCount(1, $logger->records);
        $record = $logger->records[0];
        $this->assertSame('error', $record['level']);
        $this->assertInstanceOf(QueryException::class, $record['context']['exception']);
        $this->assertSame(CreateInvoiceMode::MANUAL, $record['context']['mode']);
    }

    public function test_the_transaction_is_rolled_back_even_when_logging_fails(): void
    {
        $this->recordLogs(failing: true);
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, OrderStatus::G_PAID);
        $before = $this->writtenRowCounts();
        $levelBefore = DB::transactionLevel();

        try {
            $this->service->createRecordUsingStrategy($this->manualStrategy(
                $customer->customer_id,
                [new InvoiceProductData(987654, 1, 1.00)],
                orderIds: [$order->order_id],
            ));
            $this->fail('Expected an exception');
        } catch (\Throwable) {
        }

        $this->assertSame($levelBefore, DB::transactionLevel(), 'the rollback must happen before anything is logged');
        $this->assertSame($before, $this->writtenRowCounts());
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }
}
