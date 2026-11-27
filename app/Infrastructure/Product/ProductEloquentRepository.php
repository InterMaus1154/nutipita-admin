<?php

namespace App\Infrastructure\Product;

use App\Domain\Product\Contracts\ProductRepository;
use App\Models\CustomerProductPrice;

class ProductEloquentRepository implements ProductRepository
{

    public function findPriceForProductAndCustomer(int $productId, int $customerId): ?float
    {
        return CustomerProductPrice::query()
            ->select('customer_product_price as price')
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first()
            ?->price;
    }
}
