<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // order list
    public function index()
    {
        $orders = Order::with('customer:customer_id,customer_name', 'products')->get();
        return view('orders.index', compact('orders'));
    }

    // show create form
    public function create()
    {
        return view('orders.create');
    }
}
