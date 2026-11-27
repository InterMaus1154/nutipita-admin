<?php

namespace Tests\Feature\Domain\Invoice\Strategies;

use App\Domain\Invoice\Data\CreateInvoiceData;
use App\Domain\Invoice\Data\InvoiceProductData;
use App\Domain\Invoice\Entities\DeliveryCharge;
use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Entities\ManualInvoiceProductData;
use App\Domain\Invoice\Exceptions\InvoiceHasPricelessProductException;
use App\Domain\Invoice\Exceptions\InvoiceNumberAlreadyTakenException;
use App\Domain\Invoice\Exceptions\NoProductLineException;
use App\Domain\Invoice\Services\InvoiceService;
use App\Domain\Invoice\Strategies\CreateInvoiceStrategyFactory;
use App\Domain\Invoice\Strategies\CreateManualInvoiceStrategy;
use App\Domain\Order\Exceptions\OrderAlreadyInvoicedException;
use App\Models\Customer;
use App\Models\CustomerProductPrice;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\TestHelpers;

class CreateManualInvoiceStrategyTest extends TestCase
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

    private function priceFor(Customer $customer, Product $product, float $price): void
    {
        CustomerProductPrice::create([
            'customer_id' => $customer->customer_id,
            'product_id' => $product->product_id,
            'customer_product_price' => $price,
        ]);
    }

    /**
     * @param array<int, array{Product, int}> $lines pairs of product and quantity, in the order entered
     */
    private function strategyFor(
        Customer        $customer,
        array           $lines,
        bool            $attachOrders = true,
        string          $number = '0500',
        ?DeliveryCharge $charge = null,
        ?float          $credit = null,
    ): CreateManualInvoiceStrategy
    {
        $products = array_map(fn(array $line) => new ManualInvoiceProductData($line[0]->product_id, $line[1]), $lines);

        return $this->app->make(CreateInvoiceStrategyFactory::class)->manual(
            customerId: $customer->customer_id,
            dueDate: Carbon::parse('2026-03-20'),
            issueDate: Carbon::parse('2026-03-10'),
            periodFrom: Carbon::parse('2026-03-01'),
            periodTo: Carbon::parse('2026-03-07'),
            invoiceNumber: $number,
            products: $products,
            attachOrders: $attachOrders,
            deliveryCharge: $charge,
            credit: $credit,
        );
    }

    /**
     * @return array<int, InvoiceProductData> the lines keyed by product id
     */
    private function lines(CreateInvoiceData $data): array
    {
        return collect($data->products)->keyBy(fn(InvoiceProductData $line) => $line->productId)->all();
    }

    // ====
    // the data it builds
    // ====

    public function test_it_passes_the_form_values_through(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);

        $data = $this->strategyFor($customer, [[$pita, 2]], number: '0500', charge: DeliveryCharge::fromString('5'), credit: 12.5)->normalize();

        $this->assertSame($customer->customer_id, $data->customerId);
        $this->assertSame('0500', $data->number);
        $this->assertSame('2026-03-10', $data->issueDate->toDateString());
        $this->assertSame('2026-03-20', $data->dueDate->toDateString());
        $this->assertSame('2026-03-01', $data->from->toDateString());
        $this->assertSame('2026-03-07', $data->to->toDateString());
        $this->assertSame('5', $data->deliveryCharge->toDbString());
        $this->assertSame(12.5, $data->credit);
    }

    public function test_the_price_is_the_customers_price_and_the_quantity_is_as_entered(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $bagel = $this->createProduct();
        $this->priceFor($customer, $pita, 0.35);
        $this->priceFor($customer, $bagel, 2.00);
        // another customer pays something else for the same product
        $this->priceFor($this->createCustomer(), $pita, 9.99);

        $lines = $this->lines($this->strategyFor($customer, [[$pita, 10], [$bagel, 3]])->normalize());

        $this->assertCount(2, $lines);
        $this->assertSame(10, $lines[$pita->product_id]->quantity);
        $this->assertEqualsWithDelta(0.35, $lines[$pita->product_id]->unitPrice, 0.00001);
        $this->assertSame(3, $lines[$bagel->product_id]->quantity);
        $this->assertEqualsWithDelta(2.00, $lines[$bagel->product_id]->unitPrice, 0.00001);
    }

    public function test_the_same_product_entered_twice_is_summed_into_one_line(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);

        $lines = $this->lines($this->strategyFor($customer, [[$pita, 10], [$pita, 5]])->normalize());

        $this->assertCount(1, $lines);
        $this->assertSame(15, $lines[$pita->product_id]->quantity);
    }

    public function test_quantities_of_zero_or_less_are_skipped_and_need_no_price(): void
    {
        $customer = $this->createCustomer();
        $ordered = $this->createProduct();
        $notOrdered = $this->createProduct(); // no price for this customer, and none is needed
        $this->priceFor($customer, $ordered, 1.00);

        $lines = $this->lines($this->strategyFor($customer, [[$ordered, 4], [$notOrdered, 0], [$notOrdered, -2]])->normalize());

        $this->assertCount(1, $lines);
        $this->assertArrayHasKey($ordered->product_id, $lines);
    }

    public function test_it_does_not_write_anything(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);
        $order = $this->createOrder($customer, dueDate: '2026-03-03');

        $this->strategyFor($customer, [[$pita, 2]])->normalize();

        $this->assertDatabaseCount('invoices', 0);
        $this->assertDatabaseCount('invoice_orders', 0);
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }

    // ====
    // the orders of the period
    // ====

    public function test_it_takes_the_customers_orders_inside_the_period_inclusive(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);
        $onFrom = $this->createOrder($customer, dueDate: '2026-03-01');
        $inside = $this->createOrder($customer, dueDate: '2026-03-04');
        $onTo = $this->createOrder($customer, dueDate: '2026-03-07');
        $this->createOrder($customer, dueDate: '2026-02-28');      // the day before
        $this->createOrder($customer, dueDate: '2026-03-08');      // the day after
        $this->createOrder($this->createCustomer(), dueDate: '2026-03-04'); // someone else's

        $data = $this->strategyFor($customer, [[$pita, 1]])->normalize();

        $this->assertEqualsCanonicalizing([$onFrom->order_id, $inside->order_id, $onTo->order_id], $data->orderIds);
    }

    public function test_a_period_without_orders_is_valid(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);
        $this->createOrder($customer, dueDate: '2026-05-01'); // outside the period

        $data = $this->strategyFor($customer, [[$pita, 1]])->normalize();

        $this->assertSame([], $data->orderIds);
        $this->assertCount(1, $data->products);
    }

    public function test_without_attaching_orders_none_are_linked_even_when_the_period_has_some(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);
        $this->createOrder($customer, dueDate: '2026-03-03');

        $data = $this->strategyFor($customer, [[$pita, 1]], attachOrders: false)->normalize();

        $this->assertSame([], $data->orderIds);
    }

    public function test_without_attaching_orders_an_already_invoiced_order_does_not_block(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);
        $order = $this->createOrder($customer, dueDate: '2026-03-03');
        $this->createInvoice(customer: $customer)->orders()->attach($order->order_id);

        $data = $this->strategyFor($customer, [[$pita, 1]], attachOrders: false)->normalize();

        $this->assertCount(1, $data->products);
    }

    // ====
    // failures
    // ====

    public function test_it_fails_when_an_order_in_the_period_is_already_invoiced(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);
        $order = $this->createOrder($customer, dueDate: '2026-03-03');
        $existing = $this->createInvoice(customer: $customer);
        $existing->orders()->attach($order->order_id);

        try {
            $this->strategyFor($customer, [[$pita, 1]])->normalize();
            $this->fail('Expected OrderAlreadyInvoicedException');
        } catch (OrderAlreadyInvoicedException $e) {
            $this->assertStringContainsString((string)$order->order_id, $e->getMessage());
            $this->assertStringContainsString($existing->invoice_number, $e->getMessage());
        }
    }

    public function test_it_fails_when_the_invoice_number_is_taken(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);
        $taken = $this->createInvoice();

        $this->expectException(InvoiceNumberAlreadyTakenException::class);
        $this->expectExceptionMessage($taken->invoice_number);

        $this->strategyFor($customer, [[$pita, 1]], number: $taken->invoice_number)->normalize();
    }

    public function test_a_product_without_a_price_for_the_customer_fails(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($this->createCustomer(), $pita, 1.00); // only someone else has a price

        try {
            $this->strategyFor($customer, [[$pita, 2]])->normalize();
            $this->fail('Expected InvoiceHasPricelessProductException');
        } catch (InvoiceHasPricelessProductException $e) {
            $this->assertStringContainsString('#' . $pita->product_id, $e->getMessage());
            $this->assertStringContainsString('#' . $customer->customer_id, $e->getMessage());
        }
    }

    public function test_a_price_of_zero_counts_as_no_price(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 0.00);

        $this->expectException(InvoiceHasPricelessProductException::class);

        $this->strategyFor($customer, [[$pita, 2]])->normalize();
    }

    public function test_it_fails_when_no_products_are_given(): void
    {
        $customer = $this->createCustomer();

        $this->expectException(NoProductLineException::class);

        $this->strategyFor($customer, [])->normalize();
    }

    public function test_it_fails_when_every_quantity_is_zero(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);

        $this->expectException(NoProductLineException::class);

        $this->strategyFor($customer, [[$pita, 0]])->normalize();
    }

    // ====
    // creating the invoice through the service
    // ====

    private function create(CreateManualInvoiceStrategy $strategy): Invoice
    {
        return $this->app->make(InvoiceService::class)->createRecordUsingStrategy($strategy);
    }

    public function test_the_service_saves_the_lines_at_the_customer_price_and_adds_the_delivery_charge(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 2.50);

        $invoice = $this->create($this->strategyFor($customer, [[$pita, 4]], charge: DeliveryCharge::fromString('5'), credit: 3.0));

        $row = DB::table('invoices')->where('invoice_id', $invoice->invoice_id)->first();
        $this->assertEqualsWithDelta(15.00, (float)$row->invoice_total, 0.001); // 4 x 2.50 + 5
        $this->assertEqualsWithDelta(3.00, (float)$row->invoice_credit, 0.001);
        $line = DB::table('invoice_products')->where('invoice_id', $invoice->invoice_id)->first();
        $this->assertSame(4, $line->product_qty);
        $this->assertEqualsWithDelta(2.50, (float)$line->product_unit_price, 0.0001);
    }

    public function test_the_service_links_the_periods_orders_and_sets_them_unpaid(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);
        $a = $this->createOrder($customer, dueDate: '2026-03-02');
        $b = $this->createOrder($customer, dueDate: '2026-03-06');
        $outside = $this->createOrder($customer, dueDate: '2026-03-20');

        $invoice = $this->create($this->strategyFor($customer, [[$pita, 1]]));

        $linked = DB::table('invoice_orders')->where('invoice_id', $invoice->invoice_id)->pluck('order_id')->all();
        $this->assertEqualsCanonicalizing([$a->order_id, $b->order_id], $linked);
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($a));
        $this->assertSame('O_DELIVERED_UNPAID', $this->storedOrderStatus($b));
        $this->assertSame('G_PAID', $this->storedOrderStatus($outside));
    }

    public function test_the_service_without_attaching_orders_leaves_the_orders_alone(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);
        $order = $this->createOrder($customer, dueDate: '2026-03-03');

        $invoice = $this->create($this->strategyFor($customer, [[$pita, 1]], attachOrders: false));

        $this->assertDatabaseHas('invoices', ['invoice_id' => $invoice->invoice_id]);
        $this->assertDatabaseCount('invoice_orders', 0);
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }

    public function test_the_service_creates_an_invoice_for_a_period_without_orders(): void
    {
        $customer = $this->createCustomer();
        $pita = $this->createProduct();
        $this->priceFor($customer, $pita, 1.00);

        $invoice = $this->create($this->strategyFor($customer, [[$pita, 3]]));

        $row = DB::table('invoices')->where('invoice_id', $invoice->invoice_id)->first();
        $this->assertSame('2026-03-01', $row->invoice_from);
        $this->assertSame('2026-03-07', $row->invoice_to);
        $this->assertDatabaseCount('invoice_orders', 0);
    }
}
