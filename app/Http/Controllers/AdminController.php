<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show main dashboard
     */
    public function showDashboard()
    {
        $products = Product::select(['product_id', 'product_name'])->get();
        $orders = Order::with('products')->where('order_due_at', now()->toDateString())->get();

        // calculate total income for the day
        $totalDayIncome = $orders->sum('total_price');

        // calculate the total quantity per product for the current day
        // each specific product qty are added from all orders
        $productTotals = [];
        foreach ($orders as $order) {
            foreach ($order->products as $product) {
                $total = $order->getTotalOfProduct($product);
                isset($productTotals[$product->product_id]) ?
                    $productTotals[$product->product_id] += $total : $productTotals[$product->product_id] = $total;
            }
        }

        return view('admin.index', compact('orders', 'products', 'totalDayIncome', 'productTotals'));
    }
}
