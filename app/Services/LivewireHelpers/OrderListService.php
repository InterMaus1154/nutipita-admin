<?php

namespace App\Services\LivewireHelpers;

use Illuminate\Database\Eloquent\Builder;

/**
 * Collection of util methods for OrderList livewire component
 */
class OrderListService
{
    /**
     * Custom order sorting
     * @param $sortDirection
     * @return array
     */
    public static function customSorts($sortDirection): array
    {
        return [
            'customer' => function (Builder $query) use ($sortDirection) {
                $query->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
                    ->orderBy('customers.customer_name', $sortDirection)
                    ->select('orders.*');
            },
            'total_pita' => function (Builder $query) use ($sortDirection) {
                $query->leftJoin('order_product', 'order_product.order_id', '=', 'orders.order_id')
                    ->select('orders.*')
                    ->selectRaw('SUM(order_product.product_qty) as total_pita')
                    ->groupBy('orders.order_id', 'orders.customer_id', 'orders.order_status', 'orders.is_daytime', 'orders.is_standing', 'orders.order_placed_at', 'orders.order_due_at', 'orders.created_at', 'orders.updated_at')
                    ->orderBy('total_pita', $sortDirection);
            },
            'total_price' => function (Builder $query) use ($sortDirection) {
                $query->leftJoin('order_product', 'order_product.order_id', '=', 'orders.order_id')
                    ->select('orders.*')
                    ->selectRaw('SUM(order_product.product_qty * order_product.order_product_unit_price) as total_price')
                    ->groupBy('orders.order_id', 'orders.customer_id', 'orders.order_status', 'orders.is_daytime', 'orders.is_standing', 'orders.order_placed_at', 'orders.order_due_at', 'orders.created_at', 'orders.updated_at')
                    ->orderBy('total_price', $sortDirection);
            }
        ];
    }

    /**
     * returns the route url with an array of order ids, that are to be passed on creating a summary pdf
     * @param $orderIds
     * @return string
     */
    public static function getOrderSummaryPdfUrl($orderIds): string
    {
        return route('orders.create-summary-pdf', ['orderIds' => $orderIds]);
    }
}
