<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\CustomerProductPrice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Factory as ViewFactory;
use Throwable;

class CustomerController extends Controller
{
    /**
     * Check if received product price is null, or same as default unit price
     * On both cases, return false -> do nothing
     * @param float $priceToCompareTo
     * @param float|null $newPrice
     * @return bool
     */
    private function isProductPriceValid(float $priceToCompareTo, ?float $newPrice): bool
    {
        if (is_null($newPrice) || $priceToCompareTo == $newPrice) {
            return false;
        }
        return true;
    }

    /*
     * Show customer list page
     */
    public function index(): ViewFactory
    {
        $customers = Customer::query()
            ->select('customer_id', 'customer_name', 'customer_address', 'created_at', 'customer_email', 'customer_phone')
            ->withCount('orders')
            ->get();
        return view('customers.index', compact('customers'));
    }

    /*
     * Show create customer form
     */
    public function create(): ViewFactory
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

    /*
     * Store custom prices for a customer
     */
    public function storeCustomPrice(Request $request, Customer $customer)
    {
        foreach ($request->array('products') as $key => $value) {

            // ignore null values
            // ignore values that are same as default price
            if (!$this->isProductPriceValid(Product::find($key)->product_unit_price, $value)) {
                continue;
            }

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
        if (!$customer->customPrices()->exists()) {
            return redirect()->route('customers.create.custom-price', compact('customer'));
        }
        $products = Product::all()->map(function ($product) use ($customer) {
            return $product->setCurrentCustomer($customer);
        });
        return view('customers.edit-custom-price', compact('customer', 'products'));
    }
}
