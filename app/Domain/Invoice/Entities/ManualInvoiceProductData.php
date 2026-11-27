<?php

namespace App\Domain\Invoice\Entities;

class ManualInvoiceProductData
{
    public function __construct(public int $productId, public int $qty)
    {
    }
}
