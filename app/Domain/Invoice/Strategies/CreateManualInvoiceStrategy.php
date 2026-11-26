<?php

namespace App\Domain\Invoice\Strategies;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Data\CreateInvoiceData;
use App\Domain\Invoice\Data\InvoiceProductData;
use App\Domain\Invoice\Entities\DeliveryCharge;
use App\Domain\Invoice\Entities\ManualInvoiceProductData;
use App\Domain\Invoice\Enums\CreateInvoiceMode;
use App\Domain\Invoice\Exceptions\InvoiceHasPricelessProductException;
use App\Domain\Invoice\Exceptions\InvoiceNumberAlreadyTakenException;
use App\Domain\Invoice\Exceptions\NoProductLineException;
use App\Domain\Invoice\Querying\InvoiceQuery;
use App\Domain\Order\Contracts\OrderRepository;
use App\Domain\Order\Exceptions\OrderAlreadyInvoicedException;
use App\Domain\Product\Contracts\ProductRepository;
use App\Models\Order;
use Carbon\Carbon;

class CreateManualInvoiceStrategy extends AbstractCreateInvoiceStrategy
{
    /**
     * @param InvoiceRepository $invoices
     * @param OrderRepository $orders
     * @param ProductRepository $productRepository
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
     */
    public function __construct(
        private InvoiceRepository $invoices,
        private OrderRepository   $orders,
        private ProductRepository $productRepository,
        private int               $customerId,
        private Carbon            $dueDate,
        private Carbon            $issueDate,
        private Carbon            $periodFrom,
        private Carbon            $periodTo,
        private string            $invoiceNumber,
        private array             $products,
        private bool              $attachOrders = true,
        private ?DeliveryCharge   $deliveryCharge = null,
        private ?float            $credit = null,
    )
    {
    }

    public function mode(): CreateInvoiceMode
    {
        return CreateInvoiceMode::MANUAL;
    }

    public function makeCreateInvoiceData(): CreateInvoiceData
    {
        $orderIds = [];

        if ($this->attachOrders) {
            $orders = $this->orders->findForCustomerInDateRange($this->customerId, $this->periodFrom, $this->periodTo);

            $invoicedOrder = $orders->firstWhere(fn(Order $order) => !is_null($order->getInvoiceId()));

            if (!is_null($invoicedOrder)) {
                $invoice = $this->invoices->findById($invoicedOrder->getInvoiceId());
                throw new OrderAlreadyInvoicedException($invoicedOrder->getId(), $invoice->getInvoiceNumber());
            }

            $orderIds = $orders->map(fn(Order $order) => $order->getId())->all();
        }

        $invoiceWithNumber = $this->invoices->find(new InvoiceQuery()->invoiceNumber($this->invoiceNumber))->first();

        if (!is_null($invoiceWithNumber)) {
            throw new InvoiceNumberAlreadyTakenException($this->invoiceNumber);
        }

        $quantities = [];
        foreach ($this->products as $productData) {
            if ($productData->qty <= 0) {
                continue;
            }

            // the caller should not send duplicate, but in case it happens, sum the quantities
            $quantities[$productData->productId] = ($quantities[$productData->productId] ?? 0) + $productData->qty;
        }

        $products = [];
        foreach ($quantities as $productId => $qty) {
            $price = $this->productRepository->findPriceForProductAndCustomer($productId, $this->customerId);

            if (is_null($price) || $price <= 0) {
                throw new InvoiceHasPricelessProductException($productId, $this->customerId);
            }

            $products[] = new InvoiceProductData(
                productId: $productId,
                quantity: $qty,
                unitPrice: $price);
        }

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
            credit: $this->credit,
        );
    }
}
