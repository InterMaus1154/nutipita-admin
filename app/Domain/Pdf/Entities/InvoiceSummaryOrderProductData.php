<?php

namespace App\Domain\Pdf\Entities;

use App\Models\Product;

class InvoiceSummaryOrderProductData
{
    public function __construct(Product $product, float $unitPrice, int $qty, float $total)
    {

    }
}
