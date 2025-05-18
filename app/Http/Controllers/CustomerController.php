<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomerController extends Controller
{
    // show customer list
    public function index()
    {
        $customers = Customer::select('customer_id', 'customer_name', 'customer_address', 'created_at', 'customer_email', 'customer_phone')->withCount('orders')->get();
        return view('customers.index', compact('customers'));
    }

    // show create customer form
    public function create()
    {
        return view('customers.create');
    }

    // store new customer
    public function store(StoreCustomerRequest $request)
    {
        try {
            Customer::create($request->validated());
            return redirect()->route('customers.index')->with('success', 'New customer successfully created!');
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('customers.create', status: 500)->withErrors(['error' => $e->getMessage()]);
        }
    }

    // show a customer
    public function show(Customer $customer)
    {
        $customer->loadMissing('orders', 'customPrices');
        return view('customers.show', compact('customer'));
    }

    // show edit form
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    // update customer details
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        try {
            $customer->update($request->validated());
            return redirect()->route('customers.show', compact('customer'))
                ->with('success', 'Customer successfully updated!');
        } catch (Throwable $e) {
            Log::error($e->getMessage());;
            return redirect()->route('customers.edit', status: 500)->withErrors(['error' => $e->getMessage()]);
        }
    }
}
