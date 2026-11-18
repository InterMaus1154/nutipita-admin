<?php

namespace App\Domain\Invoice\Services;

use App\DataTransferObjects\InvoiceDto;
use App\DataTransferObjects\InvoiceProductDto;
use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Enums\InvoiceStatus;
use App\Domain\Invoice\Exceptions\CannotDeleteInvoiceFileException;
use App\Domain\Invoice\Exceptions\CannotUpdateInvoiceStatusException;
use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;
use App\Domain\Order\OrderRepository;
use App\Enums\OrderStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Psr\Log\LoggerInterface;

class InvoiceService
{

    public function __construct(private InvoiceRepository $invoices, private OrderRepository $orders, private ConnectionInterface $db, private LoggerInterface $logger)
    {
    }

    /**
     * @param InvoiceDto $invoiceDto
     * @return Invoice
     */
    public function generateInvoice(InvoiceDto $invoiceDto): Invoice
    {
        return $invoiceDto->customer()->invoices()->create([
            'invoice_number' => $invoiceDto->invoiceNumber(),
            'invoice_issue_date' => $invoiceDto->invoiceIssueDate()->toDateString(),
            'invoice_due_date' => $invoiceDto->invoiceDueDate()->toDateString(),
            'invoice_from' => $invoiceDto->invoiceOrdersFrom()?->toDateString(),
            'invoice_to' => $invoiceDto->invoiceOrdersTo()?->toDateString(),
            'invoice_status_new' => $invoiceDto->invoiceStatus()->value,
            'invoice_name' => $invoiceDto->invoiceName(),
            'invoice_path' => 'invoices/' . $invoiceDto->invoiceName(),
            'order_id' => $invoiceDto->orderId(),
            'invoice_delivery_charge' => $invoiceDto->invoiceDeliveryCharge()?->toDbString(),
            'invoice_credit' => $invoiceDto->invoiceCredit()
        ]);
    }

    /**
     * @param mixed $data
     * @return \Barryvdh\DomPDF\PDF
     */
    private function generateInvoicePdf(mixed $data): \Barryvdh\DomPDF\PDF
    {
        return Pdf::loadView('pdf.invoice', $data)
            ->setPaper('a4', 'portrait');
    }

    /**
     * Returns a collection of InvoiceProductDto
     * $products is a type of Collection of Product models
     * @param Invoice $invoice
     * @param $products
     * @return Collection
     */
    public function generateInvoiceProductDTOs(Invoice $invoice, $products): Collection
    {
        $invoiceProductDtos = collect();
        foreach ($products as $orderProduct) {
            $invoiceProductDtos->add(InvoiceProductDto::from(
                invoice: $invoice,
                product: $orderProduct,
                productQty: $orderProduct->pivot->product_qty,
                productUnitPrice: $orderProduct->pivot->order_product_unit_price
            ));
        }
        return $invoiceProductDtos;
    }

    /**
     * Generate invoice_products record from a collection of invoice product dtos
     * @param Collection<int, InvoiceProductDto> $invoiceProductDTOs
     * @return void
     */
    public function generateInvoiceProductRecords(Collection $invoiceProductDTOs): void
    {
        // create invoice product records from dto
        $invoiceProductDTOs->each(function (InvoiceProductDto $invoiceProductDto) {
            $invoiceProductDto->invoice()->products()->create([
                'product_id' => $invoiceProductDto->product()->product_id,
                'product_qty' => $invoiceProductDto->productQty(),
                'product_unit_price' => $invoiceProductDto->productUnitPrice()
            ]);
        });
    }

    public function generateInvoicePdfFromDtos(Collection $invoiceProductDtos, ?Invoice $invoice = null)
    {
        $invoiceTotal = 0;
        $productTotals = [];
        $invoiceProductDtos->each(function (InvoiceProductDto $invoiceProductDto) use (&$invoiceTotal, &$productTotals) {
            // calculate total price
            $invoiceTotal += ($invoiceProductDto->productQty() * $invoiceProductDto->productUnitPrice());

            // check if product already exists
            if (!isset($productTotals[$invoiceProductDto->product()->product_id])) {
                // if product doesn't exist yet
                $productTotals[$invoiceProductDto->product()->product_id]['total_quantity'] = $invoiceProductDto->productQty();
                $productTotals[$invoiceProductDto->product()->product_id]['unit_price'] = $invoiceProductDto->productUnitPrice();
                $productTotals[$invoiceProductDto->product()->product_id]['product_id'] = $invoiceProductDto->product()->product_id;
                $productTotals[$invoiceProductDto->product()->product_id]['product_name'] = $invoiceProductDto->product()->product_name;
                $productTotals[$invoiceProductDto->product()->product_id]['product_weight_g'] = $invoiceProductDto->product()->product_weight_g;
            } else {
                $productTotals[$invoiceProductDto->product()->product_id]['total_quantity'] += $invoiceProductDto->productQty();
            }
        });

        if ($invoiceProductDtos->isNotEmpty()) {
            $invoice = $invoiceProductDtos->first()->invoice();
        }

        $customer = $invoice->customer;

        return $this->generateInvoicePdf([
            'fromBulk' => true,
            'customer' => $customer,
            'products' => $productTotals,
            'totalPrice' => $invoiceTotal,
            'invoice' => $invoice
        ]);
    }

    /**
     * @param int $id
     * @return void
     * @throws \Throwable
     */
    public function delete(int $id): void
    {
        $this->db->beginTransaction();
        try {
            $invoice = $this->invoices->findById($id);
            if (is_null($invoice)) {
                throw new InvoiceNotFoundException($id);
            }

            $orderIds = $invoice->getOrderIds();

            $this->orders->updateStatusByIds($orderIds, OrderStatus::Y_CONFIRMED);
            $this->invoices->delete($id);
            $this->deleteInvoiceFile($invoice->getInvoicePath());
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            report($e);
            throw $e;
        }
    }

    private function deleteInvoiceFile(string $path): void
    {
        try {
            Storage::disk('local')->delete($path);
        } catch (\Throwable $e) {
            $exception = new CannotDeleteInvoiceFileException($e->getMessage());
            throw $exception;
        }
    }

    /**
     * Updates invoice status and the status of orders belonging to it
     * @param int $invoiceId
     * @param InvoiceStatus $status
     * @return void
     * @throws CannotUpdateInvoiceStatusException
     */
    public function updateStatus(int $invoiceId, InvoiceStatus $status): void
    {
        $this->db->beginTransaction();
        try {
            $invoice = $this->invoices->findById($invoiceId);
            if (is_null($invoice)) {
                throw new InvoiceNotFoundException($invoiceId);
            }

            $orderIds = $invoice->getOrderIds();
            $invoice->setInvoiceStatus($status);

            $orderStatus = match ($status) {
                InvoiceStatus::UNPAID, InvoiceStatus::LOSS => OrderStatus::O_DELIVERED_UNPAID,
                InvoiceStatus::PAID => OrderStatus::G_PAID,
                InvoiceStatus::CANCELLED => OrderStatus::Y_CONFIRMED,
            };

            $this->orders->updateStatusByIds($orderIds, $orderStatus);

            $this->invoices->commit($invoice);
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();

            if ($e instanceof InvoiceNotFoundException) {
                $message = sprintf("Couldn't update invoice status #%s as invoice not found", $invoiceId);
                $this->logger->error($message);
            } else {
                $message = sprintf("Couldn't update invoice status #%s", $invoiceId);
                $this->logger->error($message, ['e' => $e->getMessage()]);
            }
            throw new CannotUpdateInvoiceStatusException($message, $e);
        }
    }

}
