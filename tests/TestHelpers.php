<?php

namespace Tests;

use App\Domain\Invoice\Invoice;
use App\Domain\Invoice\InvoiceStatus;
use App\Models\Customer;
use App\Models\Product;

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
