<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /*
     * Show order list page
     */
    public function index(): View
    {
        $orders = Order::with('customer:customer_id,customer_name', 'products')->get();
        return view('orders.index', compact('orders'));
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

        $products = $request->array('products');
        // track if product amount is 0
        $emptyProductTracker = 0;
        foreach ($products as $product_id => $amount) {
            if ($amount == 0) continue;
            $emptyProductTracker++;
        }

        // if remains 0, all products amount is 0, which is invalid for an order
        if ($emptyProductTracker === 0) {
            return redirect()
                ->route('orders.create')
                ->withErrors(['invalid_amount' => 'At least one product need to be more than 0!']);
        }
    }
}
