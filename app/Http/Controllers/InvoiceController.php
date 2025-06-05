<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

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
//        return view('pdf.invoice', compact('order', 'invoice', 'customer', 'products'));
        return Pdf::loadView('pdf.invoice', compact('order', 'invoice', 'customer', 'products'))
            ->setPaper('a4', 'portrait')
            ->stream($invoiceName . ".pdf");
    }

    public function index()
    {
        return view('invoices.index');
    }

    public function create()
    {
        return view('invoices.create');
    }

    /*
     * Download a PDF from an invoice
     */
    public function download(Invoice $invoice)
    {
        $path = Storage::disk('local')->path($invoice->invoice_path);
        return response()->download($path);
    }
}
