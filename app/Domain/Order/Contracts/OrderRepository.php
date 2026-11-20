<?php

namespace App\Domain\Order\Contracts;

use App\Enums\OrderStatus;
use App\Models\Order;

interface OrderRepository
{
    /**
     * @param int[] $ids
     * @param OrderStatus $status
     * @return int
     */
    public function updateStatusByIds(array $ids, OrderStatus $status): int;

    public function findById(int $id): ?Order;
}
