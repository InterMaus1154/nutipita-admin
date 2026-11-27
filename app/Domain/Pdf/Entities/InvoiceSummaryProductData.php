<?php

namespace App\Domain\Pdf\Entities;

use App\Models\Product;

class InvoiceSummaryProductData
{
    public function __construct(public Product $product, public int $qty, public float $unitPrice, public float $total)
    {
    }
}
