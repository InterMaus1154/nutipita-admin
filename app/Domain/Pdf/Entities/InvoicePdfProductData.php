<?php

namespace App\Domain\Pdf\Entities;

use App\Models\Product;

class InvoicePdfProductData
{
    public function __construct(public Product $product, public float $unitPrice, public int $quantity, public float $productTotal)
    {
    }
}
