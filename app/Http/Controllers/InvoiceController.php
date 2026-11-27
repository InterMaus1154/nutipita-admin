<?php

namespace App\Http\Controllers;

use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Pdf\Exceptions\CannotGeneratePdfException;
use App\Domain\Pdf\Exceptions\NoItemsForInvoiceSummaryException;
use App\Domain\Pdf\Services\InvoiceSummaryPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function __construct(private InvoiceSummaryPdfService $summaryPdf)
    {
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
    public function download(Invoice|int $invoiceId)
    {
        $invoice = Invoice::find($invoiceId);
        if (is_null($invoice)) {
            abort(404);
        }

        $filename = 'INV-' . $invoice->invoice_number . 'pdf';
        $path = Storage::disk('local')->path($invoice->invoice_path);

        if (!Storage::disk('local')->exists($invoice->invoice_path)) {
            abort(404);
        }

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

    public function createInvoiceSummary(int $id): Response|RedirectResponse
    {
        try {
            // an unknown invoice throws InvoiceNotFoundException, which is a 404 and handled as such
            return $this->summaryPdf->forId($id)->download();
        } catch (NoItemsForInvoiceSummaryException|CannotGeneratePdfException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
