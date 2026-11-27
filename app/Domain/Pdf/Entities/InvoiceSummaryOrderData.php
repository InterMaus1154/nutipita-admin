<?php

namespace App\Domain\Pdf\Entities;

use Carbon\Carbon;

class InvoiceSummaryOrderData
{
    /**
     * @param Carbon $due
     * @param InvoiceSummaryOrderProductData[] $products
     */
    public function __construct(public Carbon $due, public array $products)
    {
    }
}
