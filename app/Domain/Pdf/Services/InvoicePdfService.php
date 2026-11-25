<?php

namespace App\Domain\Pdf\Services;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;
use App\Domain\Pdf\Entities\GeneratedPdf;
use App\Domain\Pdf\Factories\PdfRendererFactory;

class InvoicePdfService extends AbstractPdfService
{
    public function __construct(PdfRendererFactory $pdfRendererFactory, private InvoiceRepository $invoices)
    {
        parent::__construct($pdfRendererFactory);
    }

    public function forInvoice(int $invoiceId): GeneratedPdf
    {
        $invoice = $this->invoices->findById($invoiceId);
        if(is_null($invoice)) {
            throw new InvoiceNotFoundException($invoiceId);
        }

        return $this->render();
    }
}
