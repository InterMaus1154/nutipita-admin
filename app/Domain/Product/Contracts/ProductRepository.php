<?php

namespace App\Domain\Product\Contracts;

interface ProductRepository
{
    public function findPriceForProductAndCustomer(int $productId, int $customerId): ?float;

}
