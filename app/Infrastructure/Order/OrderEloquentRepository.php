<?php

namespace App\Infrastructure\Order;

use App\Domain\Order\OrderRepository;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class OrderEloquentRepository implements OrderRepository
{
    /**
     * @param int[] $ids
     * @param OrderStatus $status
     * @return int
     */
    public function updateStatusByIds(array $ids, OrderStatus $status): int
    {
        return $this->query()
            ->whereIn('order_id', $ids)
            ->update([
                'order_status' => $status->name,
            ]);
    }

    /**
     * @return Builder<Order>
     */
    private function query(): Builder
    {
        return Order::query();
    }
}
