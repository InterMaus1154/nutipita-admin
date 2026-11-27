<?php

namespace Tests\Feature\Domain\Invoice\Strategies;

use App\Domain\Invoice\Data\CreateInvoiceData;
use App\Domain\Invoice\Data\InvoiceProductData;
use App\Domain\Invoice\Entities\DeliveryCharge;
use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Exceptions\InvoiceNumberAlreadyTakenException;
use App\Domain\Invoice\Exceptions\NoProductLineException;
use App\Domain\Invoice\Services\InvoiceService;
use App\Domain\Invoice\Strategies\CreateAutoInvoiceStrategy;
use App\Domain\Invoice\Strategies\CreateInvoiceStrategyFactory;
use App\Domain\Order\Exceptions\OrderAlreadyInvoicedException;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\TestHelpers;

class CreateAutoInvoiceStrategyTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    protected function setUp(): void
    {
        parent::setUp();

        // the service tests below create invoices, which also write a pdf
        Storage::fake('local');
        Storage::fake('invoices');
    }

    private function strategyFor(Customer $customer, string $from = '2026-03-01', string $to = '2026-03-07', string $number = '0500', ?DeliveryCharge $charge = null, ?float $credit = null): CreateAutoInvoiceStrategy
    {
        return $this->app->make(CreateInvoiceStrategyFactory::class)->auto(
            customerId: $customer->customer_id,
            dueDate: Carbon::parse('2026-03-20'),
            issueDate: Carbon::parse('2026-03-10'),
            periodFrom: Carbon::parse($from),
            periodTo: Carbon::parse($to),
            invoiceNumber: $number,
            deliveryCharge: $charge,
            credit: $credit,
        );
    }

    private function orderWithLine(Customer $customer, string $dueDate, Product $product, int $qty, float $unitPrice): Order
    {
        $order = $this->createOrder($customer, dueDate: $dueDate);
        $order->products()->attach($product->product_id, [
            'product_qty' => $qty,
            'order_product_unit_price' => $unitPrice,
        ]);

        return $order;
    }

    /**
     * @return array<string, InvoiceProductData> the lines keyed by "productId@price"
     */
    private function lines(CreateInvoiceData $data): array
    {
        return collect($data->products)
            ->keyBy(fn(InvoiceProductData $line) => $line->productId . '@' . number_format($line->unitPrice, 2))
            ->all();
    }

    // ====
    // the data it builds
    // ====

    public function test_it_passes_the_form_values_through(): void
    {
        $customer = $this->createCustomer();
        $this->orderWithLine($customer, '2026-03-03', $this->createProduct(), 2, 1.00);

        $data = $this->strategyFor($customer, number: '0500', charge: DeliveryCharge::fromString('5'), credit: 12.5)->normalize();

        $this->assertSame($customer->customer_id, $data->customerId);
        $this->assertSame('0500', $data->number);
        $this->assertSame('2026-03-10', $data->issueDate->toDateString());
        $this->assertSame('2026-03-20', $data->dueDate->toDateString());
        $this->assertSame('2026-03-01', $data->from->toDateString());
        $this->assertSame('2026-03-07', $data->to->toDateString());
        $this->assertSame('5', $data->deliveryCharge->toDbString());
        $this->assertSame(12.5, $data->credit);
    }

    public function test_the_period_is_the_picked_one_and_not_the_first_and_last_order_dates(): void
    {
        $customer = $this->createCustomer();
        $this->orderWithLine($customer, '2026-03-04', $this->createProduct(), 1, 1.00);

        $data = $this->strategyFor($customer, '2026-03-01', '2026-03-07')->normalize();

        $this->assertSame('2026-03-01', $data->from->toDateString());
        $this->assertSame('2026-03-07', $data->to->toDateString());
    }

    public function test_it_only_takes_the_customers_orders_inside_the_period_inclusive(): void
    {
        $customer = $this->createCustomer();
        $product = $this->createProduct();
        $onFrom = $this->orderWithLine($customer, '2026-03-01', $product, 1, 1.00);
        $inside = $this->orderWithLine($customer, '2026-03-04', $this->createProduct(), 1, 1.00);
        $onTo = $this->orderWithLine($customer, '2026-03-07', $this->createProduct(), 1, 1.00);
        $this->orderWithLine($customer, '2026-02-28', $this->createProduct(), 1, 1.00);   // the day before
        $this->orderWithLine($customer, '2026-03-08', $this->createProduct(), 1, 1.00);   // the day after
        $this->orderWithLine($this->createCustomer(), '2026-03-04', $this->createProduct(), 1, 1.00); // someone else's

        $data = $this->strategyFor($customer)->normalize();

        $this->assertEqualsCanonicalizing(
            [$onFrom->order_id, $inside->order_id, $onTo->order_id],
            $data->orderIds
        );
    }

    public function test_a_single_order_keeps_its_quantity_and_price(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->orderWithLine($customer, '2026-03-03', $pita, 4, 1.50);

        $lines = $this->lines($this->strategyFor($customer)->normalize());

        $this->assertCount(1, $lines);
        $this->assertSame(4, $lines[$pita->product_id . '@1.50']->quantity);
    }

    public function test_the_same_product_at_the_same_price_is_summed_across_orders(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->orderWithLine($customer, '2026-03-02', $pita, 10, 0.35);
        $this->orderWithLine($customer, '2026-03-03', $pita, 5, 0.35);
        $this->orderWithLine($customer, '2026-03-05', $pita, 1, 0.35);

        $lines = $this->lines($this->strategyFor($customer)->normalize());

        $this->assertCount(1, $lines);
        $this->assertSame(16, $lines[$pita->product_id . '@0.35']->quantity);
    }

    public function test_the_same_product_at_different_prices_gives_one_line_per_price(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->orderWithLine($customer, '2026-03-02', $pita, 100, 0.30);
        $this->orderWithLine($customer, '2026-03-03', $pita, 40, 0.35);
        $this->orderWithLine($customer, '2026-03-04', $pita, 60, 0.30);

        $lines = $this->lines($this->strategyFor($customer)->normalize());

        $this->assertCount(2, $lines);
        $this->assertSame(160, $lines[$pita->product_id . '@0.30']->quantity);
        $this->assertSame(40, $lines[$pita->product_id . '@0.35']->quantity);
    }

    public function test_different_products_stay_on_separate_lines(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $bagel = $this->createProduct();
        $this->orderWithLine($customer, '2026-03-02', $pita, 3, 1.00);
        $this->orderWithLine($customer, '2026-03-03', $bagel, 7, 2.00);

        $lines = $this->lines($this->strategyFor($customer)->normalize());

        $this->assertCount(2, $lines);
        $this->assertSame(3, $lines[$pita->product_id . '@1.00']->quantity);
        $this->assertSame(7, $lines[$bagel->product_id . '@2.00']->quantity);
    }

    public function test_it_does_not_write_anything(): void
    {
        $customer = $this->createCustomer();
        $order = $this->orderWithLine($customer, '2026-03-03', $this->createProduct(), 2, 1.00);

        $this->strategyFor($customer)->normalize();

        $this->assertDatabaseCount('invoices', 0);
        $this->assertDatabaseCount('invoice_orders', 0);
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }

    // ====
    // failures
    // ====

    public function test_it_fails_when_an_order_in_the_period_is_already_invoiced(): void
    {
        $customer = $this->createCustomer();
        $order = $this->orderWithLine($customer, '2026-03-03', $this->createProduct(), 1, 1.00);
        $existing = $this->createInvoice(customer: $customer);
        $existing->orders()->attach($order->order_id);

        try {
            $this->strategyFor($customer)->normalize();
            $this->fail('Expected OrderAlreadyInvoicedException');
        } catch (OrderAlreadyInvoicedException $e) {
            $this->assertStringContainsString((string)$order->order_id, $e->getMessage());
            $this->assertStringContainsString($existing->invoice_number, $e->getMessage());
        }
    }

    public function test_it_fails_when_an_order_is_invoiced_through_the_legacy_order_id(): void
    {
        $customer = $this->createCustomer();
        $order = $this->orderWithLine($customer, '2026-03-03', $this->createProduct(), 1, 1.00);
        $this->createInvoice(customer: $customer)->update(['order_id' => $order->order_id]);

        $this->expectException(OrderAlreadyInvoicedException::class);

        $this->strategyFor($customer)->normalize();
    }

    public function test_an_invoiced_order_outside_the_period_does_not_block_it(): void
    {
        $customer = $this->createCustomer();
        $this->orderWithLine($customer, '2026-03-03', $this->createProduct(), 1, 1.00);
        $outside = $this->orderWithLine($customer, '2026-04-20', $this->createProduct(), 1, 1.00);
        $this->createInvoice(customer: $customer)->orders()->attach($outside->order_id);

        $data = $this->strategyFor($customer)->normalize();

        $this->assertCount(1, $data->orderIds);
    }

    public function test_it_fails_when_the_invoice_number_is_taken(): void
    {
        $customer = $this->createCustomer();
        $this->orderWithLine($customer, '2026-03-03', $this->createProduct(), 1, 1.00);
        $taken = $this->createInvoice();

        try {
            $this->strategyFor($customer, number: $taken->invoice_number)->normalize();
            $this->fail('Expected InvoiceNumberAlreadyTakenException');
        } catch (InvoiceNumberAlreadyTakenException $e) {
            $this->assertStringContainsString($taken->invoice_number, $e->getMessage());
        }
    }

    public function test_it_fails_when_the_period_has_no_orders(): void
    {
        $customer = $this->createCustomer();
        $this->orderWithLine($customer, '2026-05-01', $this->createProduct(), 1, 1.00); // outside the period

        $this->expectException(NoProductLineException::class);

        $this->strategyFor($customer)->normalize();
    }

    public function test_it_fails_when_the_orders_have_no_products(): void
    {
        $customer = $this->createCustomer();
        $this->createOrder($customer, dueDate: '2026-03-03');

        $this->expectException(NoProductLineException::class);

        $this->strategyFor($customer)->normalize();
    }

    // ====
    // creating the invoice through the service
    // ====

    private function create(CreateAutoInvoiceStrategy $strategy): Invoice
    {
        return $this->app->make(InvoiceService::class)->createRecordUsingStrategy($strategy);
    }

    public function test_the_service_saves_the_invoice_links_every_order_and_sets_them_unpaid(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $a = $this->orderWithLine($customer, '2026-03-02', $pita, 10, 1.00);
        $b = $this->orderWithLine($customer, '2026-03-04', $pita, 5, 1.00);
        $outside = $this->orderWithLine($customer, '2026-03-20', $pita, 9, 1.00);

        $invoice = $this->create($this->strategyFor($customer, number: '0500'));

        $linked = DB::table('invoice_orders')->where('invoice_id', $invoice->invoice_id)->pluck('order_id')->all();
        $this->assertEqualsCanonicalizing([$a->order_id, $b->order_id], $linked);
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($a));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($b));
        $this->assertSame('G_PAID', $this->storedOrderStatus($outside));
        $this->assertSame(15, (int)DB::table('invoice_products')->where('invoice_id', $invoice->invoice_id)->value('product_qty'));
    }

    public function test_the_service_total_includes_the_delivery_charge_and_keeps_the_credit_apart(): void
    {
        $customer = $this->createCustomer();
        $this->orderWithLine($customer, '2026-03-03', $this->createProduct(), 4, 2.50); // 10.00

        $invoice = $this->create($this->strategyFor($customer, charge: DeliveryCharge::fromString('5'), credit: 3.0));

        $row = DB::table('invoices')->where('invoice_id', $invoice->invoice_id)->first();
        $this->assertEqualsWithDelta(15.00, (float)$row->invoice_total, 0.001);
        $this->assertEqualsWithDelta(3.00, (float)$row->invoice_credit, 0.001);
        $this->assertSame('2026-03-01', $row->invoice_from);
        $this->assertSame('2026-03-07', $row->invoice_to);
    }

    public function test_the_service_saves_both_prices_of_a_product(): void
    {
        // needs the primary key of invoice_products to include the unit price
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->orderWithLine($customer, '2026-03-02', $pita, 100, 0.30);
        $this->orderWithLine($customer, '2026-03-03', $pita, 40, 0.35);

        $invoice = $this->create($this->strategyFor($customer));

        $this->assertSame(2, DB::table('invoice_products')->where('invoice_id', $invoice->invoice_id)->where('product_id', $pita->product_id)->count());
        $this->assertEqualsWithDelta(44.00, (float)DB::table('invoices')->where('invoice_id', $invoice->invoice_id)->value('invoice_total'), 0.001); // 30 + 14
    }

    public function test_creating_twice_for_the_same_period_fails_the_second_time(): void
    {
        $customer = $this->createCustomer();
        $this->orderWithLine($customer, '2026-03-03', $this->createProduct(), 1, 1.00);
        $this->create($this->strategyFor($customer, number: '0500'));

        try {
            $this->create($this->strategyFor($customer, number: '0501'));
            $this->fail('Expected the overlap to be refused');
        } catch (\App\Domain\Invoice\Exceptions\CannotCreateInvoiceException $e) {
            $this->assertInstanceOf(OrderAlreadyInvoicedException::class, $e->getPrevious());
        }

        $this->assertDatabaseCount('invoices', 1);
    }
}
