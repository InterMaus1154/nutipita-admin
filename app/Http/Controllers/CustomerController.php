<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
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
//            $customer = Customer::create([
//                'customer_name' => $request->validated('customer_name'),
//                'customer_phone' => $request->validated('customer_phone'),
//                'customer_email' => $request->validated('customer_email'),
//                'customer_address' => $request->validated()
//            ]);
            $customer = Customer::create($request->validated());
            return redirect()->route('customers.show', compact('customer'), status: 201);
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
}
