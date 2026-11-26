<?php

namespace App\Domain\Invoice\Strategies;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Entities\DeliveryCharge;
use App\Domain\Order\Contracts\OrderRepository;
use Carbon\Carbon;

class CreateInvoiceStrategyFactory
{
    public function __construct(private OrderRepository $orders, private InvoiceRepository $invoices)
    {
    }

    public function single(int $orderId): CreateSingleInvoiceStrategy
    {
        return new CreateSingleInvoiceStrategy($orderId, $this->orders);
    }

    public function auto(int $customerId, Carbon $dueDate, Carbon $issueDate, Carbon $periodFrom, Carbon $periodTo, string $invoiceNumber, ?DeliveryCharge $deliveryCharge = null, ?float $credit = null): CreateAutoInvoiceStrategy
    {
        return new CreateAutoInvoiceStrategy($customerId, $dueDate, $issueDate, $periodFrom, $periodTo, $invoiceNumber, $this->invoices, $this->orders, $deliveryCharge, $credit);
    }

    public function manual(): CreateManualInvoiceStrategy
    {
        return new CreateManualInvoiceStrategy();
    }
}
