<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use function Spatie\LaravelPdf\Support\pdf;

class AdminController extends Controller
{
    private function getTodaysOrdersData(): array
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
                isset($productTotals[$product->product_name]) ?
                    $productTotals[$product->product_name] += $total : $productTotals[$product->product_name] = $total;
            }
        }

        $totalDayPita = 0;
        foreach ($productTotals as $total) {
            $totalDayPita += $total;
        }

        return compact('orders', 'products', 'totalDayIncome', 'productTotals', 'totalDayPita');
    }

    /**
     * Show main dashboard
     */
    public function showDashboard()
    {
        return view('admin.index', $this->getTodaysOrdersData());
    }

    public function createOrderTotalPdf()
    {
        return pdf()
            ->view('pdf.order-total', $this->getTodaysOrdersData())
            ->name('order-total-' . now()->toDateString())
            ->download();
    }
}
