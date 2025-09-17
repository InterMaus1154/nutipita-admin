<?php

namespace App\Queries;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class OrderQueryBuilder
{
    public static function build(array $filters): Builder
    {
        $query = Order::query()
            ->when(!empty($filters['active_period']) && $filters['active_period'] === 'today', function (Builder $query) {
                $query->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->whereDate('orders.order_due_at', now()->toDateString())
                            ->where('orders.is_daytime', true);
                    })
                        ->orWhere(function ($q2) {
                            $q2->whereDate('orders.order_due_at', now()->addDay()->toDateString())
                                ->where('orders.is_daytime', false);
                        });
                });
            })
            ->when($filters['nighttime_only'], function ($builder) {
                return $builder->where('is_daytime', false);
            })
            ->when($filters['daytime_only'], function ($builder) {
                return $builder->where('orders.is_daytime', true);
            })
            ->when(!empty($filters['customer_id']), function ($builder) use ($filters) {
                return $builder->where('orders.customer_id', $filters['customer_id']);
            })
            ->when(!empty($filters['status']), function ($builder) use ($filters) {
                return $builder->where('order_status', $filters['status']);
            })
            ->with('customer:customer_id,customer_name', 'products');

        if (empty($filters['active_period']) || $filters['active_period'] !== 'today') {
            $query->when(!empty($filters['due_from']), function ($builder) use ($filters) {
                return $builder->whereDate('order_due_at', '>=', $filters['due_from']);
            })
                ->when(!empty($filters['due_to']), function ($builder) use ($filters) {
                    return $builder->whereDate('order_due_at', '<=', $filters['due_to']);
                });
        }

        return $query;
    }
}
