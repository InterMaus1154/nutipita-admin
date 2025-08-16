<?php

namespace App\Services;

use App\DataTransferObjects\OrderSummaryDto;
use App\Models\Order;
use Illuminate\Support\Collection;

class OrderService
{
    /**
     * Create a basic summary without customer from orders
     * Includes product totals and total (no income)
     * @param array $filters
     * @return OrderSummaryDto
     */
    public function createBasicSummary(array $filters): OrderSummaryDto
    {
        $orders = Order::query()
            ->when($filters['nighttime_only'], function ($builder) {
                return $builder->where('is_daytime', false);
            })
            ->when($filters['daytime_only'], function ($builder) {
                return $builder->where('orders.is_daytime', true);
            })
            ->when(!empty($filters['due_from']), function ($builder) use ($filters) {
                return $builder->whereDate('order_due_at', '>=', $filters['due_from']);
            })
            ->when(!empty($filters['due_to']), function ($builder) use ($filters) {
                return $builder->whereDate('order_due_at', '<=', $filters['due_to']);
            })
            ->when(!empty($filters['both']), function($builder) use($filters){
                return $builder->whereDate('order_due_at', '=', today()->toDateString())
                    ->where('is_daytime', true)
                    ->orWhereDate('order_due_at', '=', today()->addDay()->toDateString());
            })
            ->with('customer:customer_id,customer_name', 'products')
            ->get();

        return OrderSummaryDto::from($orders);
    }
}
