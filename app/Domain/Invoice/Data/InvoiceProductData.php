<?php

namespace App\Domain\Invoice\Data;

final readonly class InvoiceProductData
{
    public function __construct(public int   $productId,
                                public int   $quantity,
                                public float $unitPrice,
    )
    {
    }
}
