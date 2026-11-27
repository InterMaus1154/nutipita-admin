<?php

namespace App\Domain\Pdf\Entities;

use App\Models\Product;

class InvoiceSummaryOrderProductData
{
    public function __construct(public Product $product, public float $unitPrice, public int $qty, public float $total)
    {

    }
}
