<?php

namespace App\Domain\Invoice\Strategies;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Entities\DeliveryCharge;
use App\Domain\Invoice\Entities\ManualInvoiceProductData;
use App\Domain\Order\Contracts\OrderRepository;
use App\Domain\Product\Contracts\ProductRepository;
use Carbon\Carbon;

class CreateInvoiceStrategyFactory
{
    public function __construct(private OrderRepository $orders, private InvoiceRepository $invoices, private ProductRepository $products)
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

    /**
     * @param int $customerId
     * @param Carbon $dueDate
     * @param Carbon $issueDate
     * @param Carbon $periodFrom
     * @param Carbon $periodTo
     * @param string $invoiceNumber
     * @param ManualInvoiceProductData[] $products
     * @param bool $attachOrders
     * @param DeliveryCharge|null $deliveryCharge
     * @param float|null $credit
     * @return CreateManualInvoiceStrategy
     */
    public function manual(int $customerId, Carbon $dueDate, Carbon $issueDate, Carbon $periodFrom, Carbon $periodTo, string $invoiceNumber, array $products, bool $attachOrders = true, ?DeliveryCharge $deliveryCharge = null, ?float $credit = null): CreateManualInvoiceStrategy
    {
        return new CreateManualInvoiceStrategy(
            invoices: $this->invoices,
            orders: $this->orders,
            productRepository: $this->products,
            customerId: $customerId,
            dueDate: $dueDate,
            issueDate: $issueDate,
            periodFrom: $periodFrom,
            periodTo: $periodTo,
            invoiceNumber: $invoiceNumber,
            products: $products,
            attachOrders: $attachOrders,
            deliveryCharge: $deliveryCharge,
            credit: $credit,
        );
    }
}
