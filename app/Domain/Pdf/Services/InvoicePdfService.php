<?php

namespace App\Domain\Pdf\Services;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;
use App\Domain\Pdf\Entities\GeneratedPdf;
use App\Domain\Pdf\Entities\InvoicePdfData;
use App\Domain\Pdf\Entities\InvoicePdfProductData;
use App\Domain\Pdf\Exceptions\NoProductLinesForInvoicePdfException;
use App\Domain\Pdf\Factories\PdfRendererFactory;
use App\Models\InvoiceProduct;

class InvoicePdfService extends AbstractPdfService
{
    public function __construct(PdfRendererFactory $pdfRendererFactory, private InvoiceRepository $invoices)
    {
        parent::__construct($pdfRendererFactory);
    }

    public function forId(int $invoiceId): GeneratedPdf
    {
        $invoice = $this->invoices->findById($invoiceId);
        if (is_null($invoice)) {
            throw new InvoiceNotFoundException($invoiceId);
        }

        $products = $invoice->products;

        if ($products->isEmpty()) {
            throw new NoProductLinesForInvoicePdfException($invoice->getInvoiceNumber());
        }

        /** @var InvoicePdfProductData[] $productsData */
        $productsData = [];

        /** @var InvoiceProduct $invoiceProduct */
        foreach ($products as $invoiceProduct) {
            $productsData[] = new InvoicePdfProductData($invoiceProduct->product, $invoiceProduct->product_unit_price, $invoiceProduct->product_qty, $invoiceProduct->product_unit_price * $invoiceProduct->product_qty);
        }

        $data = new InvoicePdfData($invoice, $invoice->getCustomer(), $productsData, $invoice->getTotal());

        return $this->render($invoice->getFilename(), 'pdf.invoice', ['data' => $data]);
    }
}
