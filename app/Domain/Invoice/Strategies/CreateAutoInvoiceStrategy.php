<?php

namespace App\Domain\Invoice\Strategies;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Data\CreateInvoiceData;
use App\Domain\Invoice\Data\InvoiceProductData;
use App\Domain\Invoice\Entities\DeliveryCharge;
use App\Domain\Invoice\Enums\CreateInvoiceMode;
use App\Domain\Invoice\Exceptions\InvoiceNumberAlreadyTakenException;
use App\Domain\Invoice\Exceptions\NoProductLineException;
use App\Domain\Invoice\Querying\InvoiceQuery;
use App\Domain\Order\Contracts\OrderRepository;
use App\Domain\Order\Exceptions\OrderAlreadyInvoicedException;
use App\Models\Order;
use Carbon\Carbon;

class CreateAutoInvoiceStrategy extends AbstractCreateInvoiceStrategy
{

    public function __construct(private int $customerId, private Carbon $dueDate, private Carbon $issueDate, private Carbon $periodFrom, private Carbon $periodTo, private string $invoiceNumber, private InvoiceRepository $invoices, private OrderRepository $orders, private ?DeliveryCharge $deliveryCharge = null, private ?float $credit = null)
    {
    }

    public function mode(): CreateInvoiceMode
    {
        return CreateInvoiceMode::AUTO;
    }

    public function makeCreateInvoiceData(): CreateInvoiceData
    {
        $orders = $this->orders->findForCustomerInDateRange($this->customerId, $this->periodFrom, $this->periodTo);

        $invoicedOrder = $orders->firstWhere(fn(Order $order) => !is_null($order->getInvoiceId()));

        if (!is_null($invoicedOrder)) {
            $invoice = $this->invoices->findById($invoicedOrder->getInvoiceId());
            throw new OrderAlreadyInvoicedException($invoicedOrder->getId(), $invoice->getInvoiceNumber());
        }

        $invoiceWithNumber = $this->invoices->find(new InvoiceQuery()->invoiceNumber($this->invoiceNumber))->first();

        if (!is_null($invoiceWithNumber)) {
            throw new InvoiceNumberAlreadyTakenException($this->invoiceNumber);
        }

        $orderIds = $orders->map(fn(Order $order) => $order->getId())->all();

        $lines = [];
        foreach ($orders as $order) {
            foreach ($order->products as $product) {
                $price = $product->pivot->order_product_unit_price;
                // we group by id and price, as a period may contain same product with different prices
                $key = $product->product_id . '|' . $price;

                if (!isset($lines[$key])) {
                    $lines[$key] = ['productId' => $product->product_id, 'price' => $price, 'qty' => $product->pivot->product_qty];
                } else {
                    $lines[$key]['qty'] += $product->pivot->product_qty;
                }
            }
        }

        $products = array_values(array_map(fn(array $line) => new InvoiceProductData($line['productId'], $line['qty'], $line['price']), $lines));

        if (empty($products)) {
            throw new NoProductLineException();
        }

        return new CreateInvoiceData(
            customerId: $this->customerId,
            issueDate: $this->issueDate,
            dueDate: $this->dueDate,
            number: $this->invoiceNumber,
            deliveryCharge: $this->deliveryCharge,
            products: $products,
            orderIds: $orderIds,
            from: $this->periodFrom,
            to: $this->periodTo,
            credit: $this->credit);
    }
}
