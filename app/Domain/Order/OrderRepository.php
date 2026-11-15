<?php

namespace App\Domain\Order;

use App\Enums\OrderStatus;

interface OrderRepository
{
    /**
     * @param int[] $ids
     * @param OrderStatus $status
     * @return int
     */
    public function updateStatusByIds(array $ids, OrderStatus $status): int;
}
