<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\InvoiceDto;
use App\DataTransferObjects\InvoiceProductDto;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Create an invoice for a single order - from order list
     * @param Order $order
     * @param InvoiceService $invoiceService
     * @param Request $request
     * @return RedirectResponse
     * @throws \Throwable
     */
    public function createSingleInvoice(Order $order, InvoiceService $invoiceService, Request $request): RedirectResponse
    {
        if ($order->invoice()->exists()) {
            return redirect()->route('invoices.download', ['invoice' => $order->invoice()->first()]);
        }

        $order->loadMissing('customer', 'products');


        $invoiceDeliveryCharge = $request->input('invoice_delivery_charge');

        DB::beginTransaction();
        try {
            $invoiceDto = InvoiceDto::from(
                customer: $order->customer,
                invoiceOrdersFrom: $order->order_due_at,
                invoiceOrdersTo: $order->order_due_at,
                order: $order,
                invoiceDeliveryCharge: $invoiceDeliveryCharge
            );

            $invoice = $invoiceService->generateInvoice($invoiceDto);
            $invoiceProductDTOs = $invoiceService->generateInvoiceProductDTOs($invoice, $order->products);
            $invoiceService->generateInvoiceProductRecords($invoiceProductDTOs);

            $invoiceTotal = $invoiceProductDTOs->sum(function (InvoiceProductDto $dto) {
                    return $dto->productQty() * $dto->productUnitPrice();
                }) + ($invoiceDeliveryCharge ?? 0);

            $invoice->update([
                'invoice_total' => $invoiceTotal
            ]);

            $order->update([
                'order_status' => OrderStatus::O_DELIVERED_UNPAID->name
            ]);

            $invoiceService->generateInvoicePdfFromDtos($invoiceProductDTOs)->save($invoice->invoice_path, 'local');

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
        return view('invoices.create');
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
