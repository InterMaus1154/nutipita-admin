<?php

namespace App\Domain\Order\Contracts;

use App\Enums\OrderStatus;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface OrderRepository
{
    /**
     * @param int[] $ids
     * @param OrderStatus $status
     * @return int
     */
    public function updateStatusByIds(array $ids, OrderStatus $status): int;

    public function findById(int $id): ?Order;

    /**
     * @param int $customerId
     * @param Carbon $from
     * @param Carbon $to
     * @return Collection<int, Order>
     */
    public function findForCustomerInDateRange(int $customerId, Carbon $from, Carbon $to): Collection;
}
