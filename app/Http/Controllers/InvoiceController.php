<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function createSingleInvoice(Order $order, InvoiceService $invoiceService)
    {
        // check if the order already has an invoice
        if ($order->invoice()->exists()) {
            return redirect()->route('invoices.download', ['invoice' => $order->invoice()->first()]);
        }
        $order->loadMissing('customer', 'products');
        $customer = $order->customer;
        DB::beginTransaction();
        try {
            $invoice = $invoiceService->generateInvoice(customer: $customer, invoiceFrom: $order->order_due_at, invoiceTo: $order->order_due_at);
            // save the current order for the invoice
            $invoice->order_id = $order->order_id;
            $invoice->save();
            $pdf = $invoiceService->generateInvoiceDocumentFromOrders(collect([$order]), $invoice);
            $pdf->save($invoice->invoice_path, 'local');
            DB::commit();
            return redirect()->route('invoices.download', compact('invoice'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->withErrors(['invoice_error' => 'Error at creating invoice. Check log for more info']);
        }

    }

    public function index()
    {
        return view('invoices.index');
    }

    /*
     * Create invoice from orders form
     */
    public function create()
    {
        return view('invoices.create');
    }

    /*
     * Create a manual invoice
     */
    public function createManual()
    {
        return view('invoices.create-manual');
    }

    /*
     * Download a PDF from an invoice
     */
    public function download(Invoice $invoice)
    {
        $path = Storage::disk('local')->path($invoice->invoice_path);
        return response()->download($path, 'INV-'.$invoice->invoice_number.'.pdf');
    }

    /*
     * View a PDF in browser
     */
    public function viewInline(Invoice $invoice)
    {
        $path = Storage::disk('local')->path($invoice->invoice_path);
        $filename = 'INV-'.$invoice->invoice_number;
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'.pdf',
            'Cache-Control' => 'no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0'
        ]);
    }
}
