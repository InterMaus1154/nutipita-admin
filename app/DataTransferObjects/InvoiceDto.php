<?php

namespace App\DataTransferObjects;

use App\Enums\InvoiceStatus;
use App\Models\Customer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Carbon\Carbon;

class InvoiceDto
{
    private function __construct(private readonly Customer $customer, private readonly Carbon $invoiceIssueDate)
    {
    }

    public static function from(Customer|int|string $customer, Carbon|string $invoiceIssueDate, Carbon|string $invoiceDueDate, Carbon|string $invoiceOrdersFrom = null, Carbon|string $invoiceOrdersTo = null, InvoiceStatus|string $invoiceStatus = InvoiceStatus::DUE, string $invoiceNumber = null)
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

        try {
            if ($invoiceIssueDate instanceof Carbon) {

            }
        } catch (\Exception $e) {

        }


        return new InvoiceDto($customerModel);
    }

    public function customer(): Customer
    {
        return $this->customer;
    }

    /**
     * Helper function to parse dates into Carbon instances
     * @param Carbon|string|null $inputDate
     * @param string $fieldName
     * @param bool $nullable
     * @return Carbon|mixed|void
     */
    private function parseDate(Carbon|string|null $inputDate, string $fieldName, bool $nullable = false)
    {

        // if Carbon by default, simply return it
        if($inputDate instanceof Carbon){
            return $inputDate;
        }

        // if null is provided AND null is allowed, return null
        if($inputDate === null && $nullable){
            return null;
        }

        try{
            return Carbon::parse($inputDate);
        }catch(\Exception $e){
            // if invalid format is provided, which cannot be converted
            throw new \InvalidArgumentException("Invalid date format for {$fieldName}");
        }
    }
}
