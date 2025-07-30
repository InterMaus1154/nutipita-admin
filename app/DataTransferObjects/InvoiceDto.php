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
                return redirect()->route('errors.404')->with('error_message', 'Customer not found!');
            }
        }




        return new InvoiceDto($customerModel);
    }

    public function customer(): Customer
    {
        return $this->customer;
    }
}
