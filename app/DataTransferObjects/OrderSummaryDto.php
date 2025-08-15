<?php

namespace App\DataTransferObjects;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Create an order summary from orders.
 * Not customer related, it summarises orders into numbers.
 * No income.
 */
final readonly class OrderSummaryDto
{
    /**
     * @param Collection<ProductTotalDto> $productTotalDTOs
     * @param int $productTotals
     */
    private function __construct(private SupportCollection $productTotalDTOs, private int $productTotals)
    {
    }

    /**
     * @param Collection<Order> $orders
     * @return OrderSummaryDto
     */
    public static function from(Collection $orders): OrderSummaryDto
    {
        $productTotals = [];
        $orders->each(function (Order $order) use (&$productTotals) {
            $order->products->each(function (Product $product) use (&$productTotals, $order) {
                $total = $order->getTotalOfProduct($product);
                // get the total pita of each product in every order
                if (isset($productTotals[$product->product_id])) {
                    $productTotals[$product->product_id]['total'] += $total;
                } else {
                    $productTotals[$product->product_id]['total'] = $total;
                    $productTotals[$product->product_id]['name'] = $product->product_name;
                }
            });
        });

        // convert the product totals into DTOs
        $productTotalDTOs = collect();
        foreach ($productTotals as $id => $data) {
            $productTotalDTOs->add(ProductTotalDto::from($id, $data['name'], $data['total']));
        }

        // get the product totals for all product
        $productTotals = 0;

        $productTotalDTOs->each(function (ProductTotalDto $dto) use (&$productTotals) {
            $productTotals += $dto->total();
        });

        return new self($productTotalDTOs, $productTotals);
    }

    /**
     * @return SupportCollection<ProductTotalDto>
     */
    public function productTotalDTOs(): SupportCollection
    {
        return $this->productTotalDTOs;
    }

    public function productTotals(): int
    {
        return $this->productTotals;
    }
}
