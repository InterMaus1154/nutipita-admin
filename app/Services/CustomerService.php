<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{

    public function findById(int $id): ?Customer
    {
        return Customer::where('customer_id', $id)->first();
    }

    public function commit(Customer $customer): Customer
    {
        return tap($customer)->save();
    }
}
