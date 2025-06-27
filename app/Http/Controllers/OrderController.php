<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class OrderController extends Controller
{
    /*
     * Show order list page
     */
    public function index(): View
    {
        return view('orders.index');
    }

    /*
     * Show create order form
     */
    public function create(): View
    {
        return view('orders.create');
    }

    /*
     * Create new order
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $customer_id = $request->validated('customer_id');
        $order_placed_at = $request->validated('order_placed_at');
        $order_due_at = $request->validated('order_due_at');

        $products = collect($request->array('products', []));

        // products with more than 0 quantity
        $products = $products->filter(function ($qty) {
            return $qty > 0;
        });

        // if empty, all products amount is 0, which is invalid for an order
        if ($products->isEmpty()) {
            return redirect()
                ->route('orders.create')
                ->withErrors(['invalid_amount' => 'At least one product need to be more than 0!'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $customer = Customer::find($customer_id);
            // create order for customer
            $order = $customer->orders()->create([
                'order_status' => OrderStatus::Y_CONFIRMED->name,
                'order_placed_at' => $order_placed_at,
                'order_due_at' => $order_due_at
            ]);

            // create order products
            foreach ($products as $product_id => $qty) {
                // find product and set current customer for accurate pricing
                $product = Product::find($product_id)->setCurrentCustomer($customer);
                $order->products()->attach($product_id, [
                    'product_qty' => $qty,
                    'order_product_unit_price' => $product->price
                ]);
            }
            DB::commit();
            return redirect()
                ->route('orders.index')
                ->with('success', 'Order successfully created!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->route('orders.create')
                ->withErrors(['error' => 'Error at creating order.', 'error_msg' => $e->getMessage()]);
        }
    }

    /*
     * Show order details page
     */
    public function show(Order $order)
    {
        $order->loadCount('products')
            ->loadMissing('customer:customer_id,customer_name', 'products.customPrices');
        return view('orders.show', compact('order'));
    }

    /*
     * Show edit order form
     */
    public function edit(Order $order)
    {
        $order->loadMissing('customer:customer_id,customer_name', 'products');
        // TODO optimise products
        $products = Product::all()->map(fn($product) => $product->setCurrentCustomer($order->customer));
        return view('orders.edit', compact('order', 'products'));
    }

    /*
     * Update an order
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->loadMissing('products');
        DB::beginTransaction();
        try {
            $order->update([
                'order_status' => $request->validated('order_status'),
                'order_placed_at' => $request->validated('order_placed_at'),
                'order_due_at' => $request->validated('order_due_at'),
                // once an order is updated, it is no longer a standing order
                'is_standing' => false
            ]);
            $orderProducts = collect($order->products)->keyBy('product_id');
            foreach ($request->array('products') as $product_id => $quantity) {
                if ($quantity <= 0) {
                    if ($orderProducts->has($product_id)) {
                        $order->products()->detach($product_id);
                    } else {
                        continue;
                    }
                } else {
                    $price = Product::find($product_id)->setCurrentCustomer($order->customer)->price;
                    if ($orderProducts->has($product_id)) {
                        $order->products()->updateExistingPivot($product_id, [
                            'product_qty' => $quantity,
                            'order_product_unit_price' => $price
                        ]);
                    } else {
                        $order->products()->attach($product_id, [
                            'product_qty' => $quantity,
                            'order_product_unit_price' => $price
                        ]);
                    }

                }
            }
            // if order had an invoice, delete it, as it is no longer "valid"
            if ($order->invoice) {
                $order->invoice()->delete();
            }
            DB::commit();
            return redirect()
                ->route('orders.index')
                ->with('success', 'Order updated successfully');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return redirect()
                ->route('orders.edit', compact('order'))
                ->withErrors(['order_update' => 'Error at updating order. No changes made']);

        }
    }
}
