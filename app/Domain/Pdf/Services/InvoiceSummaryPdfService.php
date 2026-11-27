<?php

namespace App\Domain\Pdf\Services;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Exceptions\InvoiceNotFoundException;
use App\Domain\Pdf\Entities\GeneratedPdf;
use App\Domain\Pdf\Entities\InvoiceSummaryData;
use App\Domain\Pdf\Entities\InvoiceSummaryOrderData;
use App\Domain\Pdf\Entities\InvoiceSummaryOrderProductData;
use App\Domain\Pdf\Entities\InvoiceSummaryProductData;
use App\Domain\Pdf\Enums\InvoiceSummaryDisplayMode;
use App\Domain\Pdf\Exceptions\NoItemsForInvoiceSummaryException;
use App\Domain\Pdf\Factories\PdfRendererFactory;
use App\Models\Order;
use Illuminate\Contracts\Filesystem\Factory as Filesystem;

class InvoiceSummaryPdfService extends AbstractPdfService
{
    public function __construct(PdfRendererFactory $pdfRendererFactory, Filesystem $filesystem, private InvoiceRepository $invoices)
    {
        parent::__construct($pdfRendererFactory, $filesystem);
    }

    public function forId(int $id): GeneratedPdf
    {
        $invoice = $this->invoices->findById($id);

        if (is_null($invoice)) {
            throw new InvoiceNotFoundException($id);
        }

        // if invoice has no orders, we will display products
        $displayMode = InvoiceSummaryDisplayMode::RENDER_ORDERS;

        if ($invoice->orders->isEmpty()) {
            $displayMode = InvoiceSummaryDisplayMode::RENDER_PRODUCTS;
        }

        $renderOrders = [];
        $renderProducts = [];
        if ($displayMode === InvoiceSummaryDisplayMode::RENDER_ORDERS) {

            /** @var Order $order */
            foreach ($invoice->orders as $order) {

                $products = [];
                foreach ($order->products as $product) {
                    $products[] = new InvoiceSummaryOrderProductData(
                        product: $product,
                        unitPrice: $product->pivot->order_product_unit_price,
                        qty: $product->pivot->product_qty,
                        total: $product->pivot->order_product_unit_price * $product->pivot->product_qty,
                    );
                }

                $renderOrders[] = new InvoiceSummaryOrderData(
                    due: $order->getDueDate(),
                    products: $products,
                );
            }
        }

        if ($displayMode === InvoiceSummaryDisplayMode::RENDER_PRODUCTS) {
            foreach ($invoice->products as $invoiceProduct) {
                $product = $invoiceProduct->product;
                $renderProducts[] = new InvoiceSummaryProductData(
                    product: $product,
                    qty: $invoiceProduct->product_qty,
                    unitPrice: $invoiceProduct->product_unit_price,
                    total: $invoiceProduct->product_qty * $invoiceProduct->product_unit_price,
                );
            }
        }

        if (empty($renderOrders) && empty($renderProducts)) {
            throw new NoItemsForInvoiceSummaryException($invoice->getInvoiceNumber());
        }

        $pdfData = new InvoiceSummaryData(
            displayMode: $displayMode,
            invoice: $invoice,
            orderData: $renderOrders,
            productData: $renderProducts
        );

        $filename = sprintf('INV-%s-Summary.pdf', $invoice->getInvoiceNumber());

        return $this->render($filename, 'pdf.invoice-summary', ['data' => $pdfData]);
    }
}
