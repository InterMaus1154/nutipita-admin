<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use function Spatie\LaravelPdf\Support\pdf;

class InvoiceController extends Controller
{
    public function test()
    {
        $order = Order::with('customer', 'products')->first();
        $customer = $order->customer;
        $products = $order->products->map(function (Product $product) use ($customer) {
            return $product->setCurrentCustomer($customer);
        });
        $invoice = $order->invoices()->first();
        $invoiceNumber = $invoice->invoice_number;
        $invoiceName = "#" . $order->order_id . "-" . $invoiceNumber . '-' . $order->order_due_at;
//        return view('pdf.invoice');
        return pdf()
            ->format('a4')
            ->view('pdf.invoice', compact('order', 'invoiceNumber', 'customer', 'products'))
            ->name($invoiceName);
    }
}
