<?php

namespace App\Domain\Invoice\Strategies;

use App\Domain\Order\Contracts\OrderRepository;

class CreateInvoiceStrategyFactory
{
    public function __construct(private OrderRepository $orders)
    {
    }

    public function single(int $orderId): CreateSingleInvoiceStrategy
    {
        return new CreateSingleInvoiceStrategy($orderId, $this->orders);
    }
}
