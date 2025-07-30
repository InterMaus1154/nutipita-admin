<?php

namespace App\DataTransferObjects;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Carbon\Carbon;

class InvoiceDto
{
    private function __construct(private readonly Customer      $customer,
                                 private readonly Carbon        $invoiceIssueDate,
                                 private readonly Carbon        $invoiceDueDate,
                                 private readonly Carbon|null   $invoiceOrdersFrom,
                                 private readonly Carbon|null   $invoiceOrdersTo,
                                 private readonly InvoiceStatus $invoiceStatus,
                                 private readonly string        $invoiceNumber,
                                 private readonly string        $invoiceName

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
     * @return InvoiceDto
     */
    public static function from(Customer|int|string  $customer,
                                Carbon|string|null   $invoiceIssueDate,
                                Carbon|string|null   $invoiceDueDate,
                                Carbon|string        $invoiceOrdersFrom = null,
                                Carbon|string|null   $invoiceOrdersTo = null,
                                InvoiceStatus|string $invoiceStatus = InvoiceStatus::DUE,
                                string|null          $invoiceNumber = null): InvoiceDto
    {
        // check what type of customer is provided
        // resolve string and int into a model
        if (is_int($customer) || is_string($customer)) {
            try {
                $customerModel = Customer::findOrFail($customer);
            } catch (ModelNotFoundException $e) {
                throw new NotFoundHttpException('Customer not found within invoice dto', $e);
            }
        }

        // parse date to Carbon instances
        $invoiceIssueDate = self::parseDate($invoiceIssueDate, 'Invoice issue date', true);
        $invoiceDueDate = self::parseDate($invoiceDueDate, 'Invoice due date', true);

        // set default issue date to today
        if (is_null($invoiceIssueDate)) {
            $invoiceIssueDate = now();
        }

        // set default due day to tomorrow
        if (is_null($invoiceDueDate)) {
            $invoiceDueDate = now()->addDay();
        }

        $invoiceOrdersFrom = self::parseDate($invoiceOrdersFrom, 'Invoice orders from', true);
        $invoiceOrdersTo = self::parseDate($invoiceOrdersTo, 'Invoice orders to', true);

        // if provided invoice number is null, generate the next available number
        if (is_null($invoiceNumber)) {
            $invoiceNumber = Invoice::generateInvoiceNumber();
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

        return new InvoiceDto(
            customer: $customerModel,
            invoiceIssueDate: $invoiceIssueDate,
            invoiceDueDate: $invoiceDueDate,
            invoiceOrdersFrom: $invoiceOrdersFrom,
            invoiceOrdersTo: $invoiceOrdersTo,
            invoiceStatus: $invoiceStatus,
            invoiceNumber: $invoiceNumber,
            invoiceName: $invoiceName);
    }

    /**
     * Parse date into Carbon instances
     * @param Carbon|string|null $inputDate
     * @param string $fieldName
     * @param bool $nullable
     * @return Carbon|null
     */
    private static function parseDate(Carbon|string|null $inputDate, string $fieldName, bool $nullable = false): Carbon|null
    {

        // if Carbon by default, simply return it
        if ($inputDate instanceof Carbon) {
            return $inputDate;
        }

        // if null is provided AND null is allowed, return null
        if (is_null($inputDate) && $nullable) {
            return null;
        }

        try {
            return Carbon::parse($inputDate);
        } catch (\Exception $e) {
            // if invalid format is provided, which cannot be converted
            throw new \InvalidArgumentException("Invalid date format for {$fieldName}", 422);
        }
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


}
