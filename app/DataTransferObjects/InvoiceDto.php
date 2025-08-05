<?php

namespace App\DataTransferObjects;

use App\Enums\InvoiceStatus;
use App\Helpers\Format;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Carbon\Carbon;
use App\Models\Order;

/**
 * Data transfer object for invoice details
 */
final readonly class InvoiceDto
{
    private function __construct(private Customer      $customer,
                                 private Carbon        $invoiceIssueDate,
                                 private Carbon        $invoiceDueDate,
                                 private Carbon|null   $invoiceOrdersFrom,
                                 private Carbon|null   $invoiceOrdersTo,
                                 private InvoiceStatus $invoiceStatus,
                                 private string        $invoiceNumber,
                                 private string        $invoiceName,
                                 private int|null      $orderId

    )
    {
    }

    /**
     * Create an invoice data transfer objects
     * @param Customer|int|string $customer
     * @param Carbon|string|null $invoiceIssueDate
     * @param Carbon|string|null $invoiceDueDate
     * @param Carbon|string|null $invoiceOrdersFrom
     * @param Carbon|string|null $invoiceOrdersTo
     * @param InvoiceStatus|string $invoiceStatus
     * @param string|null $invoiceNumber
     * @param Order|string|int|null $order - single order to which the invoice might belong
     * @return InvoiceDto
     */
    public static function from(Customer|int|string   $customer,
                                Carbon|string|null    $invoiceIssueDate = null,
                                Carbon|string|null    $invoiceDueDate = null,
                                Carbon|string         $invoiceOrdersFrom = null,
                                Carbon|string|null    $invoiceOrdersTo = null,
                                InvoiceStatus|string  $invoiceStatus = InvoiceStatus::DUE,
                                string|null           $invoiceNumber = null,
                                Order|string|int|null $order = null
    ): InvoiceDto
    {
        // check what type of customer is provided
        // resolve string and int into a model
        if (is_int($customer) || is_string($customer)) {
            try {
                $customer = Customer::findOrFail($customer);
            } catch (ModelNotFoundException $e) {
                throw new NotFoundHttpException('Customer not found within invoice dto', $e);
            }
        }

        // parse date to Carbon instances
        $invoiceIssueDate = Format::dateToCarbon($invoiceIssueDate, 'Invoice issue date', true);
        $invoiceDueDate = Format::dateToCarbon($invoiceDueDate, 'Invoice due date', true);

        // set default issue date to today
        if (is_null($invoiceIssueDate)) {
            $invoiceIssueDate = now();
        }

        // set default due day to tomorrow
        if (is_null($invoiceDueDate)) {
            $invoiceDueDate = now()->addDay();
        }

        $invoiceOrdersFrom = Format::dateToCarbon($invoiceOrdersFrom, 'Invoice orders from', true);
        $invoiceOrdersTo = Format::dateToCarbon($invoiceOrdersTo, 'Invoice orders to', true);

        // if provided invoice number is null, generate the next available number
        if (is_null($invoiceNumber)) {
            $invoiceNumber = Invoice::getNextInvoiceNumber();
        } else {
            // check if the provided invoice number is valid - if already exists, invalid
            if (Invoice::where('invoice_number', $invoiceNumber)->exists()) {
                throw new \InvalidArgumentException('Invoice number is already taken', 422);
            }
        }

        // generate invoice name from number
        $invoiceName = 'INV-' . $invoiceNumber . '.pdf';

        if (is_string($invoiceStatus)) {
            $invoiceStatus = InvoiceStatus::tryFrom($invoiceStatus);
            if (is_null($invoiceStatus)) {
                throw new \InvalidArgumentException('Invalid invoice status provided', 422);
            }
        }

        // get order id from Order model
        if ($order instanceof Order) {
            $order = (int)$order->order_id;
        }

        return new InvoiceDto(
            customer: $customer,
            invoiceIssueDate: $invoiceIssueDate,
            invoiceDueDate: $invoiceDueDate,
            invoiceOrdersFrom: $invoiceOrdersFrom,
            invoiceOrdersTo: $invoiceOrdersTo,
            invoiceStatus: $invoiceStatus,
            invoiceNumber: $invoiceNumber,
            invoiceName: $invoiceName,
            orderId: $order);
    }

    /*
     * Make private properties accessible via function call
     */
    /**
     * Customer, to which the invoice belongs to.
     * Returns a customer model instance
     * @return Customer
     */
    public function customer(): Customer
    {
        return $this->customer;
    }

    /**
     * Invoice issue date - the date on which the invoice is issued at.
     * Returns a Carbon date instance
     * @return Carbon
     */
    public function invoiceIssueDate(): Carbon
    {
        return $this->invoiceIssueDate;
    }

    /**
     * Invoice due date - on which the invoice should be paid.
     * Returns a Carbon date instance
     * @return Carbon
     */
    public function invoiceDueDate(): Carbon
    {
        return $this->invoiceDueDate;
    }

    /**
     * Start date of the order range for this invoice. (based on order_due_at)
     * For example, if the invoice contains orders starting from 2025-07-05,
     * that date will be returned as the "orders from" date.
     * @return Carbon|null
     */
    public function invoiceOrdersFrom(): Carbon|null
    {
        return $this->invoiceOrdersFrom;
    }

    /**
     *  Get the end date of the order range (based on `order_due_at`) for this invoice.
     *
     *  For example, if the invoice contains orders up to 2025-07-12,
     *  that date will be returned as the "orders to" date
     * @return Carbon|null
     */
    public function invoiceOrdersTo(): Carbon|null
    {
        return $this->invoiceOrdersTo;
    }

    /**
     * Return the status of an invoice - which is paid or due.
     * Returns an InvoiceStatus enum
     * @return InvoiceStatus
     */
    public function invoiceStatus(): InvoiceStatus
    {
        return $this->invoiceStatus;
    }

    /**
     * Returns a valid invoice number
     * @return string
     */
    public function invoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    /**
     * Returns the full file name of an invoice
     * Eg: INV-0001.pdf
     * @return string
     */
    public function invoiceName(): string
    {
        return $this->invoiceName;
    }

    /**
     * The id of the order to which this invoice might belong.
     * Only in case of single invoice generation from a single order
     * @return int|null
     */
    public function orderId(): int|null
    {
        return $this->orderId;
    }


}
