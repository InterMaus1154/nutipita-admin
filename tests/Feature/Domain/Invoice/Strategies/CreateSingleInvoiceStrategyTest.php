<?php

namespace Tests\Feature\Domain\Invoice\Strategies;

use App\Domain\Invoice\Data\InvoiceProductData;
use App\Domain\Invoice\Enums\CreateInvoiceMode;
use App\Domain\Invoice\Exceptions\NoProductLineException;
use App\Domain\Invoice\Strategies\CreateInvoiceStrategyFactory;
use App\Domain\Invoice\Strategies\CreateSingleInvoiceStrategy;
use App\Domain\Order\Contracts\OrderRepository;
use App\Domain\Order\Exceptions\OrderAlreadyInvoicedException;
use App\Domain\Order\Exceptions\OrderNotFoundException;
use App\Models\CustomerProductPrice;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\TestHelpers;

class CreateSingleInvoiceStrategyTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    private function strategyFor(int $orderId): CreateSingleInvoiceStrategy
    {
        return new CreateSingleInvoiceStrategy($orderId, $this->app->make(OrderRepository::class));
    }

    private function addLine(Order $order, Product $product, int $qty, float $unitPrice): void
    {
        $order->products()->attach($product->product_id, [
            'product_qty' => $qty,
            'order_product_unit_price' => $unitPrice,
        ]);
    }

    public function test_it_reports_the_single_mode(): void
    {
        $this->assertSame(CreateInvoiceMode::SINGLE, $this->strategyFor(1)->mode());
    }

    // ====
    // happy path
    // ====

    public function test_it_builds_the_invoice_data_from_the_order(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, dueDate: '2026-03-02');
        $this->addLine($order, $this->createProduct(), 4, 1.50);

        $data = $this->strategyFor($order->order_id)->normalize();

        $this->assertSame($customer->customer_id, $data->customerId);
        $this->assertSame([$order->order_id], $data->orderIds);
        $this->assertSame('2026-03-02', $data->from->toDateString());
        $this->assertSame('2026-03-02', $data->to->toDateString());
    }

    public function test_it_uses_the_prices_stored_on_the_order_lines(): void
    {
        $order = $this->createOrder();
        $bread = $this->createProduct();
        $roll = $this->createProduct();
        $this->addLine($order, $bread, 10, 0.75);
        $this->addLine($order, $roll, 3, 2.20);

        $data = $this->strategyFor($order->order_id)->normalize();

        $this->assertCount(2, $data->products);
        $this->assertContainsOnlyInstancesOf(InvoiceProductData::class, $data->products);

        $byProduct = collect($data->products)->keyBy('productId');
        $this->assertSame(10, $byProduct[$bread->product_id]->quantity);
        $this->assertSame(0.75, $byProduct[$bread->product_id]->unitPrice);
        $this->assertSame(3, $byProduct[$roll->product_id]->quantity);
        $this->assertSame(2.20, $byProduct[$roll->product_id]->unitPrice);
    }

    public function test_the_order_price_wins_over_a_changed_current_price(): void
    {
        $order = $this->createOrder();
        $product = $this->createProduct();
        $this->addLine($order, $product, 2, 1.00);
        CustomerProductPrice::create([
            'customer_id' => $order->customer_id,
            'product_id' => $product->product_id,
            'customer_product_price' => 9.99,
        ]);

        $data = $this->strategyFor($order->order_id)->normalize();

        $this->assertSame(1.00, $data->products[0]->unitPrice);
    }

    public function test_it_defaults_the_dates_to_today_and_tomorrow_and_has_no_delivery_charge(): void
    {
        $order = $this->createOrder();
        $this->addLine($order, $this->createProduct(), 1, 1.00);

        $data = $this->strategyFor($order->order_id)->normalize();

        $this->assertSame(now()->toDateString(), $data->issueDate->toDateString());
        $this->assertSame(now()->addDay()->toDateString(), $data->dueDate->toDateString());
        $this->assertNull($data->deliveryCharge);
    }

    public function test_it_accepts_zero_quantity_lines(): void
    {
        $order = $this->createOrder();
        $this->addLine($order, $this->createProduct(), 0, 1.00);

        $data = $this->strategyFor($order->order_id)->normalize();

        $this->assertCount(1, $data->products);
        $this->assertSame(0, $data->products[0]->quantity);
    }

    public function test_it_only_uses_the_requested_order(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);
        $other = $this->createOrder($customer);
        $this->addLine($order, $this->createProduct(), 1, 1.00);
        $this->addLine($other, $this->createProduct(), 5, 5.00);

        $data = $this->strategyFor($order->order_id)->normalize();

        $this->assertSame([$order->order_id], $data->orderIds);
        $this->assertCount(1, $data->products);
    }

    // ====
    // failures
    // ====

    public function test_it_throws_when_the_order_does_not_exist(): void
    {
        $this->expectException(OrderNotFoundException::class);
        $this->expectExceptionMessage('999999');

        $this->strategyFor(999999)->normalize();
    }

    public function test_it_throws_when_the_order_is_already_on_an_invoice(): void
    {
        $order = $this->createOrder();
        $this->addLine($order, $this->createProduct(), 1, 1.00);
        $invoice = $this->createInvoice(customer: $order->customer);
        $invoice->orders()->attach($order->order_id);

        try {
            $this->strategyFor($order->order_id)->normalize();
            $this->fail('Expected OrderAlreadyInvoicedException');
        } catch (OrderAlreadyInvoicedException $e) {
            $this->assertStringContainsString((string)$order->order_id, $e->getMessage());
            $this->assertStringContainsString($invoice->invoice_number, $e->getMessage());
        }
    }

    public function test_it_throws_when_the_order_is_linked_through_the_legacy_order_id(): void
    {
        $order = $this->createOrder();
        $this->addLine($order, $this->createProduct(), 1, 1.00);
        $invoice = $this->createInvoice(customer: $order->customer);
        $invoice->update(['order_id' => $order->order_id]);

        try {
            $this->strategyFor($order->order_id)->normalize();
            $this->fail('Expected OrderAlreadyInvoicedException');
        } catch (OrderAlreadyInvoicedException $e) {
            $this->assertStringContainsString($invoice->invoice_number, $e->getMessage());
        }
    }

    public function test_an_order_on_a_different_invoice_does_not_block_this_one(): void
    {
        $customer = $this->createCustomer();
        $invoiced = $this->createOrder($customer);
        $free = $this->createOrder($customer);
        $this->addLine($free, $this->createProduct(), 1, 1.00);
        $this->createInvoice(customer: $customer)->orders()->attach($invoiced->order_id);

        $data = $this->strategyFor($free->order_id)->normalize();

        $this->assertSame([$free->order_id], $data->orderIds);
    }

    public function test_it_throws_when_the_order_has_no_products(): void
    {
        $order = $this->createOrder();

        $this->expectException(NoProductLineException::class);

        $this->strategyFor($order->order_id)->normalize();
    }

    public function test_it_does_not_write_anything(): void
    {
        $order = $this->createOrder();
        $this->addLine($order, $this->createProduct(), 2, 1.00);

        $this->strategyFor($order->order_id)->normalize();

        $this->assertDatabaseCount('invoices', 0);
        $this->assertDatabaseCount('invoice_orders', 0);
        $this->assertSame('G_PAID', $this->storedOrderStatus($order));
    }

    // ====
    // factory
    // ====

    public function test_the_factory_builds_a_working_single_strategy(): void
    {
        $order = $this->createOrder();
        $this->addLine($order, $this->createProduct(), 2, 1.00);

        $strategy = $this->app->make(CreateInvoiceStrategyFactory::class)->single($order->order_id);

        $this->assertInstanceOf(CreateSingleInvoiceStrategy::class, $strategy);
        $this->assertSame($order->order_id, $strategy->orderId);
        $this->assertSame([$order->order_id], $strategy->normalize()->orderIds);
    }
}
