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
        $products = Product::query()
            ->with('customPrices')
            ->select(['product_id', 'product_name', 'product_weight_g'])
            ->orderByDesc('product_name')
            ->orderByDesc('product_weight_g')
            ->get();
        $customers = Customer::query()
            ->withCount('orders')
            ->orderBy('customer_name', 'asc')
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
            $customer = Customer::create($request->validated());
            return redirect()
                ->route('customers.edit.custom-price', compact('customer'))
                ->with('success', 'New customer successfully created!');
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('customers.create')->withErrors(['error' => $e->getMessage()]);
        }
    }

    /*
     * Show a customer details page
     */
    public function show(Customer $customer): View
    {
        $products = Product::select(['product_id', 'product_name'])->get();
        $customer->loadMissing('customPrices', 'customPrices.product');
        $orderQuery = $customer->orders()->orderByDesc('order_id')->with('products');
        $orders = (clone $orderQuery)->paginate(15);
        $ordersAll = $orderQuery->nonCancelled()->get();
        return view('customers.show', compact('customer', 'products', 'orders', 'ordersAll') + ['withSummaries' => true]);
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
            return redirect()->route('customers.edit', compact('customer'))->withErrors(['error' => $e->getMessage()]);
        }
    }

    /*
     * Show edit custom prices form
     */
    public function editCustomPrice(Customer $customer): View|RedirectResponse
    {
        $products = Product::query()
            ->select(['product_id', 'product_name', 'product_weight_g'])
            ->orderByDesc('product_name')
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
        foreach ($request->array('products') as $product_id => $price) {
            $product = Product::find($product_id)->setCurrentCustomer($customer);
            if (!$this->isProductPriceValid(0, $price)) {
                // if price is 0 or empty, delete the custom price
                $customPrice = CustomerProductPrice::query()
                    ->where('product_id', $product_id)
                    ->where('customer_id', $customer->customer_id)
                    ->first();
                if ($customPrice) {
                    $customPrice->delete();
                }
                continue;
            }

            DB::beginTransaction();
            try {
                CustomerProductPrice::updateOrCreate(
                    [
                        'customer_id' => $customer->customer_id,
                        'product_id' => $product->product_id
                    ],
                    [
                        'customer_product_price' => $price,
                        'product_id' => $product->product_id,
                        'customer_id' => $customer->customer_id
                    ]
                );
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                Log::error($e->getMessage());;
                return redirect()->route('customers.edit.custom-price', compact('customer'))->withErrors(['server_error' => $e->getMessage()]);
            }
        }
        return redirect()
            ->route('customers.index')
            ->with('success', 'Custom prices successfully updated!');
    }

}
