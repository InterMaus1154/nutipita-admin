<?php

namespace App\Domain\Pdf\Entities;

use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Entities\InvoiceTotal;
use App\Models\Customer;

class InvoicePdfData
{
    /**
     * @param Invoice $invoice
     * @param Customer $customer
     * @param InvoicePdfProductData[] $products
     * @param InvoiceTotal $invoiceTotal
     */
    public function __construct(public Invoice $invoice, public Customer $customer, public array $products, public InvoiceTotal $invoiceTotal)
    {
    }
}
