<?php

namespace App\Domain\Pdf\Entities;

use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Pdf\Enums\InvoiceSummaryDisplayMode;

class InvoiceSummaryData
{
    /**
     * @param InvoiceSummaryDisplayMode $displayMode
     * @param Invoice $invoice
     * @param InvoiceSummaryOrderData[] $orderData
     * @param InvoiceSummaryProductData[] $productData
     */
    public function __construct(public InvoiceSummaryDisplayMode $displayMode, public Invoice $invoice, public array $orderData = [], public array $productData = [])
    {
    }
}
