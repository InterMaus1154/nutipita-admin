<?php

namespace Tests;

use App\Domain\Invoice\Invoice;
use App\Enums\LegacyInvoiceStatus;
use App\Models\Customer;

trait TestHelpers
{
    protected function createCustomer(): Customer
    {
        return Customer::create([
            'customer_name' => 'Test Customer ' . uniqid(),
            'customer_address_1' => '1 Test Street',
            'customer_postcode' => 'TE5 7ST',
            'customer_city' => 'Testville',
            'customer_country' => 'United Kingdom',
        ]);
    }

    protected function createInvoice(LegacyInvoiceStatus $status = LegacyInvoiceStatus::due, float $total = 100.00): Invoice
    {
        return Invoice::create([
            'invoice_number' => Invoice::getNextInvoiceNumber(),
            'customer_id' => $this->createCustomer()->customer_id,
            'invoice_issue_date' => now(),
            'invoice_due_date' => now()->addDays(30),
            'invoice_path' => 'invoices/test.pdf',
            'invoice_name' => 'Test Invoice',
            'invoice_status' => $status->name,
            'invoice_total' => $total,
        ]);
    }
}
