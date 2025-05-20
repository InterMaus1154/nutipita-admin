<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\CustomerProductPrice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class CustomerController extends Controller
{
    private function isProductPriceValid(int $productId, ?int $value): bool
    {
        if (is_null($value)) {
            return false;
        }
        return true;
    }

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

    // show create custom price form
    public function createCustomPrice(Customer $customer)
    {
        $products = Product::whereDoesntHave('customPrices', function ($q) use ($customer) {
            return $q->where('customer_id', $customer->customer_id);
        })->get();
        if ($products->isEmpty()) {
            return redirect()->route('customers.edit.custom-price', compact('customer'));
        }
        return view('customers.create-custom-price', compact('customer', 'products'));
    }

    // store custom prices
    public function storeCustomPrice(Request $request, Customer $customer)
    {
        foreach ($request->array('products') as $key => $value) {
            // ignore null values
            if (!$this->isProductPriceValid($key, $value)) continue;

            try {
                CustomerProductPrice::create([
                    'customer_id' => $customer->customer_id,
                    'product_id' => $key,
                    'customer_product_price' => $value
                ]);
            } catch (Throwable $e) {
                Log::error($e->getMessage());;
                return redirect()->route('customers.edit.custom-price', status: 500)->withErrors(['error' => $e->getMessage()]);
            }
        };
        return redirect()
            ->route('customers.show', compact('customer'))
            ->with('success', 'Custom prices successfully added!');
    }

    // show edit price form
    public function editCustomPrice(Customer $customer)
    {
        $products = Product::all()->map(function($product) use($customer){
            return $product->setCurrentCustomer($customer);
        });
        return view('customers.edit-custom-price', compact('customer', 'products'));
    }
}
