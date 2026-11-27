<?php

namespace App\Domain\Pdf\Entities;

use App\Models\Product;

class InvoiceSummaryProductData
{
    public function __construct(Product $product, int $qty, float $unitPrice, float $total)
    {
    }
}
