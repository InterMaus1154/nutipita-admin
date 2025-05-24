<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
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
        $products = Product::all();
        $orders = Order::query()
            ->with('customer:customer_id,customer_name', 'products')
            ->select(['order_status', 'order_placed_at', 'order_due_at', 'customer_id', 'order_id', 'created_at'])
            ->orderByDesc('created_at')
            ->get();
        return view('orders.index', compact('orders', 'products'));
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
                'order_status' => OrderStatus::Y_PENDING->name,
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

    public function show(Order $order)
    {
        $order->loadCount('products')
            ->loadMissing('customer:customer_id,customer_name', 'products.customPrices');
        return view('orders.show', compact('order'));
    }
}
