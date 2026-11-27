<?php

namespace Tests\Feature\Console;

use App\Domain\Invoice\Enums\InvoiceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\TestHelpers;

class BackfillInvoiceOrdersCommandTest extends TestCase
{
    use RefreshDatabase;
    use TestHelpers;

    private function run_command(): void
    {
        $this->artisan('app:backfill-invoice-orders-command')->assertSuccessful();
    }

    private function linkedOrderIds(int $invoiceId): array
    {
        return DB::table('invoice_orders')->where('invoice_id', $invoiceId)->pluck('order_id')->sort()->values()->all();
    }

    public function test_it_links_a_single_order_invoice_through_its_order_id(): void
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, dueDate: '2026-03-02');
        $invoice = $this->createInvoice(customer: $customer);
        $invoice->update(['order_id' => $order->order_id, 'invoice_from' => '2026-03-02', 'invoice_to' => '2026-03-02']);

        $this->run_command();

        $this->assertSame([$order->order_id], $this->linkedOrderIds($invoice->invoice_id));
    }

    public function test_a_single_order_invoice_does_not_pick_up_other_orders_of_the_customer_on_the_same_day(): void
    {
        $customer = $this->createCustomer();
        $invoiced = $this->createOrder($customer, dueDate: '2026-03-02');
        $sameDay = $this->createOrder($customer, dueDate: '2026-03-02');
        $invoice = $this->createInvoice(customer: $customer);
        $invoice->update(['order_id' => $invoiced->order_id, 'invoice_from' => '2026-03-02', 'invoice_to' => '2026-03-02']);

        $this->run_command();

        $this->assertSame([$invoiced->order_id], $this->linkedOrderIds($invoice->invoice_id));
        $this->assertDatabaseMissing('invoice_orders', ['order_id' => $sameDay->order_id]);
    }

    public function test_a_single_order_invoice_is_never_matched_by_date_range_even_when_its_range_is_wider(): void
    {
        $customer = $this->createCustomer();
        $invoiced = $this->createOrder($customer, dueDate: '2026-03-02');
        $inRange = $this->createOrder($customer, dueDate: '2026-03-05');
        $invoice = $this->createInvoice(customer: $customer);
        $invoice->update(['order_id' => $invoiced->order_id, 'invoice_from' => '2026-03-01', 'invoice_to' => '2026-03-10']);

        $this->run_command();

        $this->assertSame([$invoiced->order_id], $this->linkedOrderIds($invoice->invoice_id));
        $this->assertDatabaseMissing('invoice_orders', ['order_id' => $inRange->order_id]);
    }

    public function test_it_links_a_bulk_invoice_by_customer_and_date_range(): void
    {
        $customer = $this->createCustomer();
        $first = $this->createOrder($customer, dueDate: '2026-03-02');
        $second = $this->createOrder($customer, dueDate: '2026-03-05');
        $outside = $this->createOrder($customer, dueDate: '2026-03-20');
        $otherCustomer = $this->createOrder(dueDate: '2026-03-03');
        $invoice = $this->createInvoice(customer: $customer);
        $invoice->update(['invoice_from' => '2026-03-01', 'invoice_to' => '2026-03-10']);

        $this->run_command();

        $this->assertSame([$first->order_id, $second->order_id], $this->linkedOrderIds($invoice->invoice_id));
        $this->assertDatabaseMissing('invoice_orders', ['order_id' => $outside->order_id]);
        $this->assertDatabaseMissing('invoice_orders', ['order_id' => $otherCustomer->order_id]);
    }

    public function test_it_ignores_invoices_without_an_order_id_or_a_date_range(): void
    {
        $invoice = $this->createInvoice();
        $this->createOrder($invoice->customer_id);

        $this->run_command();

        $this->assertSame([], $this->linkedOrderIds($invoice->invoice_id));
    }

    public function test_it_is_idempotent(): void
    {
        $customer = $this->createCustomer();
        $single = $this->createOrder($customer, dueDate: '2026-03-02');
        $singleInvoice = $this->createInvoice(customer: $customer);
        $singleInvoice->update(['order_id' => $single->order_id, 'invoice_from' => '2026-03-02', 'invoice_to' => '2026-03-02']);

        $bulkOrder = $this->createOrder($customer, dueDate: '2026-04-05');
        $bulkInvoice = $this->createInvoice(customer: $customer);
        $bulkInvoice->update(['invoice_from' => '2026-04-01', 'invoice_to' => '2026-04-10']);

        $this->run_command();
        $this->run_command();

        $this->assertSame([$single->order_id], $this->linkedOrderIds($singleInvoice->invoice_id));
        $this->assertSame([$bulkOrder->order_id], $this->linkedOrderIds($bulkInvoice->invoice_id));
        $this->assertSame(2, DB::table('invoice_orders')->count());
    }

    public function test_it_keeps_links_that_already_exist(): void
    {
        $customer = $this->createCustomer();
        $linked = $this->createOrder($customer, dueDate: '2026-03-02');
        $later = $this->createOrder($customer, dueDate: '2026-03-03');
        $invoice = $this->createInvoice(InvoiceStatus::PAID, customer: $customer);
        $invoice->update(['invoice_from' => '2026-03-01', 'invoice_to' => '2026-03-10']);
        $invoice->orders()->attach($linked->order_id);

        $this->run_command();

        // an invoice that already has orders is left alone
        $this->assertSame([$linked->order_id], $this->linkedOrderIds($invoice->invoice_id));
        $this->assertDatabaseMissing('invoice_orders', ['order_id' => $later->order_id]);
    }
}
