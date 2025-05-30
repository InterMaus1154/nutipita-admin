<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use function Spatie\LaravelPdf\Support\pdf;

class InvoiceController extends Controller
{
    public function createInvoice(Order $order)
    {
        $order->loadMissing('customer', 'products', 'invoice');
        if ($order->invoice) {
            $invoice = $order->invoice;
        } else {
            $invoice = $order->invoice()->create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'invoice_issue_date' => now()->toDateString()
            ]);
        }
        $customer = $order->customer;
        $products = $order->products->map(function (Product $product) use ($customer) {
            return $product->setCurrentCustomer($customer);
        });
        $invoiceNumber = $invoice->invoice_number;
        $invoiceName = 'INV-' . $invoiceNumber . '-' . now()->toDateString();
        return pdf()
            ->format('a4')
            ->view('pdf.invoice', compact('order', 'invoice', 'customer', 'products'))
            ->name($invoiceName);
    }
}
