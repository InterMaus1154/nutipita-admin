<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\CustomerProductPrice;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
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
    public function index(): View
    {
        // TODO optimise
        $products = Product::all();
        $customers = Customer::query()
            ->select('customer_id', 'customer_name', 'customer_address', 'created_at', 'customer_email', 'customer_phone')
            ->withCount('orders')
            ->get();
        return view('customers.index', compact('customers', 'products'));
    }

    /*
     * Show create customer form
     */
    public function create(): View
    {
        return view('customers.create');
    }

    /*
     * Store new customer
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        try {
            Customer::create($request->validated());
            return redirect()->route('customers.index')->with('success', 'New customer successfully created!');
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('customers.create', status: 500)->withErrors(['error' => $e->getMessage()]);
        }
    }

    /*
     * Show a customer details page
     */
    public function show(Customer $customer): View
    {
        $customer->loadMissing('customPrices', 'customPrices.product', 'orders', 'orders.products');
        return view('customers.show', compact('customer'));
    }

    /*
     * Show customer edit form
     */
    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    /*
     * Update customer details
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        try {
            $customer->update($request->validated());
            return redirect()->route('customers.index')
                ->with('success', 'Customer successfully updated!');
        } catch (Throwable $e) {
            Log::error($e->getMessage());;
            return redirect()->route('customers.edit', status: 500)->withErrors(['error' => $e->getMessage()]);
        }
    }

    /*
     * Show edit custom prices form
     */
    public function editCustomPrice(Customer $customer): View|RedirectResponse
    {
        $products = Product::query()
            ->select(['product_id', 'product_name', 'product_unit_price'])
            ->get()
            ->map(function ($product) use ($customer) {
                // set customer for each product to show custom prices for current customer
                // only available if set
                return $product->setCurrentCustomer($customer);
            });
        return view('customers.edit-custom-price', compact('customer', 'products'));
    }

    /*
     * Update or add custom prices
     */
    public function updateCustomPrice(Request $request, Customer $customer)
    {
        foreach ($request->array('products') as $key => $value) {
            $product = Product::find($key)->setCurrentCustomer($customer);
            if (!$this->isProductPriceValid($product->price, $value)) continue;

            try {
                CustomerProductPrice::updateOrCreate(
                        [
                            'customer_id' => $customer->customer_id,
                            'product_id' => $product->product_id
                        ],
                        [
                            'customer_product_price' => $value,
                            'product_id' => $product->product_id,
                            'customer_id' => $customer->customer_id
                        ]
                    );
            } catch (Throwable $e) {
                Log::error($e->getMessage());;
                return redirect()->route('customers.edit.custom-price', compact('customer'))->withErrors(['server_error' => $e->getMessage()]);
            }
        }
        return redirect()
            ->route('customers.index')
            ->with('success', 'Custom prices successfully updated!');
    }

    /*
     * Remove a custom price
     */
    public function deleteCustomPrice(Customer $customer, CustomerProductPrice $customPrice): RedirectResponse
    {
        if ($customPrice->customer_id !== $customer->customer_id) {
            return redirect()->route('customers.show')
                ->withErrors(['ownership_error' => 'This custom price doesnt belong to this customer!']);
        }

        $result = DB::transaction(function () use ($customPrice, $customer) {
            try {
                $customPrice->delete();
                return redirect()->route('customers.show', compact('customer'))
                    ->with('success', 'Custom price deleted!');
            } catch (\Exception $e) {
                return $e;
            }
        }, 3);

        if ($result instanceof \Exception) {
            Log::error($result->getMessage());
            return redirect()
                ->route('customers.show', compact('customer'))
                ->withErrors(['error' => $result->getMessage()]);
        }
        return $result;
    }
}
