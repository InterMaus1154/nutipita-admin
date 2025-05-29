<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use function Spatie\LaravelPdf\Support\pdf;

class InvoiceController extends Controller
{
    public function test()
    {
        $order = Order::first();
        $invoiceNumber = $order->invoices()->first()->invoice_number;
        $invoiceName = "#" . $order->order_id . "-" . $invoiceNumber . '-' . $order->order_due_at;
//        return view('pdf.invoice');
        return pdf()->withBrowsershot(function (Browsershot $browsershot) {

        })->format('a4')->view('pdf.invoice', compact('order', 'invoiceNumber'))->name($invoiceName);
    }
}
