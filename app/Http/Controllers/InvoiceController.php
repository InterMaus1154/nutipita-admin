<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\InvoiceDto;
use App\DataTransferObjects\InvoiceProductDto;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Create an invoice for a single order - from order list
     * @param Order $order
     * @param InvoiceService $invoiceService
     * @return RedirectResponse
     */
    public function createSingleInvoice(Order $order, InvoiceService $invoiceService): RedirectResponse
    {
        // check if the order already has an invoice
        if ($order->invoice()->exists()) {
            return redirect()->route('invoices.download', ['invoice' => $order->invoice()->first()]);
        }
        $order->loadMissing('customer', 'products');
        DB::beginTransaction();
        try {
            // create invoice
            $invoiceDto = InvoiceDto::from(
                customer: $order->customer,
                invoiceOrdersFrom: $order->order_due_at,
                invoiceOrdersTo: $order->order_due_at,
                order: $order
            );

            $invoice = $invoiceService->generateInvoice($invoiceDto);

            // generate DTOs from order products
            $invoiceProductDTOs = $invoiceService->generateInvoiceProductDTOs($invoice, $order->products);

            // create invoice product records from dto
            $invoiceProductDTOs->each(function (InvoiceProductDto $invoiceProductDto) {
                $invoiceProductDto->invoice()->products()->create([
                    'product_id' => $invoiceProductDto->product()->product_id,
                    'product_qty' => $invoiceProductDto->productQty(),
                    'product_unit_price' => $invoiceProductDto->productUnitPrice()
                ]);
            });

            $pdf = $invoiceService->generateInvoicePdfFromDtos($invoiceProductDTOs);
            $pdf->save($invoice->invoice_path, 'local');

            // generate invoice pdf
//            $pdf = $invoiceService->generateInvoiceDocumentFromOrders(collect([$order]), $invoice);
//            $pdf->save($invoice->invoice_path, 'local');
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
        $filename = 'INV-' . $invoice->invoice_number . 'pdf';
        $path = Storage::disk('local')->path($invoice->invoice_path);
        return response()->download($path, 'INV-' . $invoice->invoice_number . '.pdf', [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=$filename"
        ]);
    }

    /*
     * View a PDF in browser
     */
    public function viewInline(Invoice $invoice)
    {
        $path = Storage::disk('local')->path($invoice->invoice_path);
        $filename = 'INV-' . $invoice->invoice_number . '.pdf';
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=$filename",
            'Cache-Control' => 'no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0'
        ]);
    }
}
