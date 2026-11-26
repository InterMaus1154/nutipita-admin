<?php

namespace App\Domain\Invoice\Services;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Enums\InvoiceStatus;
use App\Domain\Invoice\Events\InvoiceCreated;
use App\Domain\Invoice\Exceptions\CannotCreateInvoiceException;
use App\Domain\Invoice\Exceptions\CannotDeleteInvoiceFileException;
use App\Domain\Invoice\Exceptions\CannotUpdateInvoiceStatusException;
use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;
use App\Domain\Invoice\Strategies\AbstractCreateInvoiceStrategy;
use App\Domain\Order\Contracts\OrderRepository;
use App\Enums\OrderStatus;
use App\Helpers\DispatcherInterface;
use App\Helpers\UserDisplayable;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Storage;
use Psr\Log\LoggerInterface;

class InvoiceService
{

    public function __construct(private InvoiceRepository $invoices, private OrderRepository $orders, private ConnectionInterface $db, private LoggerInterface $logger, private DispatcherInterface $dispatcher)
    {
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

    /**
     * @param AbstractCreateInvoiceStrategy $strategy
     * @return Invoice
     */
    public function createRecordUsingStrategy(AbstractCreateInvoiceStrategy $strategy): Invoice
    {
        $this->db->beginTransaction();
        try {
            $data = $strategy->normalize();

            $total = 0;
            foreach ($data->products as $product) {
                $total += ($product->quantity * $product->unitPrice);
            }

            if (!is_null($data->deliveryCharge)) {
                $total += $data->deliveryCharge->getCalculated();
            }

            $invoice = Invoice::make(
                customerId: $data->customerId,
                dueDate: $data->dueDate,
                issueDate: $data->issueDate,
                number: $data->number,
                total: $total,
                from: $data->from,
                to: $data->to,
                deliveryCharge: $data->deliveryCharge,
                credit: $data->credit,
            );

            $invoice = $this->invoices->commit($invoice);

            if(!empty($data->orderIds)) {
                $this->invoices->linkToOrders($invoice->getId(), $data->orderIds);
            }

            $this->invoices->linkToProducts($invoice->getId(), $data->products);

            $this->orders->updateStatusByIds($data->orderIds, OrderStatus::O_DELIVERED_UNPAID);
            $this->db->commit();

            try {
                $this->dispatcher->dispatch(new InvoiceCreated($invoice));
            }catch (\Throwable $e){
                $this->logger->warning('[InvoiceService] Failed to dispatch InvoiceCreated: ' . $e->getMessage(), ['e' => $e]);
            }

            return $invoice;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $message = sprintf('[Create Invoice] Using strategy: %s failed with message: %s', $strategy->mode()->value, $e->getMessage());
            $userFriendlyMessage = 'There was an error generating invoice.';
            if ($e instanceof UserDisplayable) {
                $userFriendlyMessage = $userFriendlyMessage . ' ' . $e->getMessage();
                $this->logger->info($message, ['exception' => $e, 'mode' => $strategy->mode()]);
            } else {
                $this->logger->error($message, ['exception' => $e, 'mode' => $strategy->mode()]);
            }
            throw new CannotCreateInvoiceException($userFriendlyMessage, previous: $e);
        }
    }
}
