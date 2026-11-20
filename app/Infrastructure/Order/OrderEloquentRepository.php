<?php

namespace App\Infrastructure\Order;

use App\Domain\Order\Contracts\OrderRepository;
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

    public function findById(int $id): ?Order
    {
        return $this->query()
            ->where('order_id', $id)
            ->with(['products', 'products.customPrices', 'invoice', 'invoices'])
            ->first();
    }
}
