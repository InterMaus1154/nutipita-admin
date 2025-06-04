<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;

class InvoiceService
{
    /**
     * @param Collection $orders
     *
     */
    public function generateInvoice(Collection $orders)
    {
        $totalPrice = 0;
        $allProducts = collect();
        $customer_id = $orders->first()->customer_id;
        $customer = Customer::find($customer_id);
        foreach ($orders as $order) {
            $totalPrice += $order->total_price;

            $allProducts = $allProducts->merge($order->products);
        }

        $groupedProducts = $allProducts
            ->groupBy('product_id')
            ->map(function ($items, $productId) use ($customer) {
                $unit_price = $items->first()->setCurrentCustomer($customer)->pricel;
                return [
                    'product_id' => $productId,
                    'product_weight_g' => $items->first()->product_weight_g,
                    'product_name' => $items->first()->product_name,
                    'total_quantity' => $items->sum('pivot.product_qty'),
                    'unit_price' => $unit_price
                ];
            });
        return Pdf::loadView('pdf.invoice', [
            'fromBulk' => true,
            'customer' => $customer,
            'products' => $groupedProducts,
            'totalPrice' => $totalPrice
        ])
            ->setPaper('a4', 'portrait');
    }
}
