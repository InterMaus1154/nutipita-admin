<?php

namespace App\Domain\Invoice\Strategies;

use App\Domain\Invoice\Data\CreateInvoiceData;
use App\Domain\Invoice\Data\InvoiceProductData;
use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Enums\CreateInvoiceMode;
use App\Domain\Invoice\Exceptions\NoProductLineException;
use App\Domain\Order\Contracts\OrderRepository;
use App\Domain\Order\Exceptions\OrderAlreadyInvoicedException;
use App\Domain\Order\Exceptions\OrderNotFoundException;
use Carbon\Carbon;

final class CreateSingleInvoiceStrategy extends AbstractCreateInvoiceStrategy
{

    public function __construct(public readonly int $orderId, private OrderRepository $orders)
    {
    }

    public function mode(): CreateInvoiceMode
    {
        return CreateInvoiceMode::SINGLE;
    }

    /**
     * @return CreateInvoiceData
     * @throws OrderNotFoundException
     */
    public function makeCreateInvoiceData(): CreateInvoiceData
    {
        $order = $this->orders->findById($this->orderId);

        if (is_null($order)) {
            throw new OrderNotFoundException($this->orderId);
        }

        if (!is_null($order->getInvoiceId())) {
            $invoice = $order->invoice ?? $order->invoices->first();
            throw new OrderAlreadyInvoicedException($this->orderId, $invoice->getInvoiceNumber());
        }

        $now = Carbon::now();

        $issueDate = $now;
        $dueDate = $now->copy()->addDay();
        $number = Invoice::getNextInvoiceNumber();

        /** @var array<int, InvoiceProductData> $products */
        $products = [];
        foreach ($order->products as $orderProduct) {
            $products[] = new InvoiceProductData(
                productId: $orderProduct->product_id,
                quantity: $orderProduct->pivot->product_qty,
                unitPrice: $orderProduct->pivot->order_product_unit_price,
            );
        }

        if (empty($products)) {
            throw new NoProductLineException();
        }

        return new CreateInvoiceData(
            customerId: $order->getCustomerId(),
            issueDate: $issueDate,
            dueDate: $dueDate,
            number: $number,
            deliveryCharge: null,
            products: $products,
            orderIds: [$this->orderId],
            from: $order->getDueDate(),
            to: $order->getDueDate(),
        );
    }

}
