<?php

namespace Tests;

use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

trait TestHelpers
{
    protected function createCustomer(?string $name = null): Customer
    {
        return Customer::create([
            'customer_name' => $name ?? 'Test Customer ' . uniqid(),
            'customer_address_1' => '1 Test Street',
            'customer_postcode' => 'TE5 7ST',
            'customer_city' => 'Testville',
            'customer_country' => 'United Kingdom',
        ]);
    }

    protected function createOrder(
        Customer|int|null $customer = null,
        OrderStatus       $status = OrderStatus::G_PAID,
        string            $dueDate = '2026-03-02',
    ): Order
    {
        $customerId = $customer instanceof Customer ? $customer->customer_id : $customer;

        return Order::create([
            'customer_id' => $customerId ?? $this->createCustomer()->customer_id,
            'order_status' => $status->name,
            'order_placed_at' => '2026-03-01',
            'order_due_at' => $dueDate,
        ]);
    }

    /**
     * The status as stored in the database, read directly so a stale model can't hide a missing update
     */
    protected function storedOrderStatus(Order|int $order): string
    {
        $id = $order instanceof Order ? $order->order_id : $order;

        return DB::table('orders')->where('order_id', $id)->value('order_status');
    }

    protected function createProduct(?string $name = null): Product
    {
        return Product::create([
            'product_name' => $name ?? 'Test Product ' . uniqid(),
        ]);
    }

    protected function createInvoice(
        InvoiceStatus $status = InvoiceStatus::UNPAID,
        float         $total = 100.00,
        ?Customer     $customer = null,
        ?string       $dueDate = null,
        ?string       $issueDate = null,
    ): Invoice
    {
        return Invoice::create([
            'invoice_number' => Invoice::getNextInvoiceNumber(),
            'customer_id' => ($customer ?? $this->createCustomer())->customer_id,
            'invoice_issue_date' => $issueDate ?? now(),
            'invoice_due_date' => $dueDate ?? now()->addDays(30),
            'invoice_path' => 'invoices/test.pdf',
            'invoice_name' => 'Test Invoice',
            'invoice_status_new' => $status->value,
            'invoice_total' => $total,
        ]);
    }
}
