<?php

namespace App\Services;

use App\DataTransferObjects\InvoiceDto;
use App\DataTransferObjects\InvoiceProductDto;
use App\Models\Customer;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class InvoiceService
{

    /**
     * @param InvoiceDto $invoiceDto
     * @return Invoice
     */
    public function generateInvoice(InvoiceDto $invoiceDto): Invoice
    {
        return $invoiceDto->customer()->invoices()->create([
            'invoice_number' => $invoiceDto->invoiceNumber(),
            'invoice_issue_date' => $invoiceDto->invoiceIssueDate()->toDateString(),
            'invoice_due_date' => $invoiceDto->invoiceDueDate()->toDateString(),
            'invoice_from' => $invoiceDto->invoiceOrdersFrom()?->toDateString(),
            'invoice_to' => $invoiceDto->invoiceOrdersTo()?->toDateString(),
            'invoice_status' => $invoiceDto->invoiceStatus()->value,
            'invoice_name' => $invoiceDto->invoiceName(),
            'invoice_path' => 'invoices/' . $invoiceDto->invoiceName(),
            'order_id' => $invoiceDto->orderId()
        ]);
    }

    /**
     * @param mixed $data
     * @return \Barryvdh\DomPDF\PDF
     */
    private function generateInvoicePdf(mixed $data): \Barryvdh\DomPDF\PDF
    {
        return Pdf::loadView('pdf.invoice', $data)
            ->setPaper('a4', 'portrait');
    }

    /**
     * Returns a collection of InvoiceProductDto
     * $products is a type of Collection of Product models
     * @param Invoice $invoice
     * @param $products
     * @return Collection
     */
    public function generateInvoiceProductDTOs(Invoice $invoice, $products): Collection
    {
        $invoiceProductDtos = collect();
        foreach ($products as $orderProduct) {
            $invoiceProductDtos->add(InvoiceProductDto::from(
                invoice: $invoice,
                product: $orderProduct,
                productQty: $orderProduct->pivot->product_qty,
                productUnitPrice: $orderProduct->pivot->order_product_unit_price
            ));
        }
        return $invoiceProductDtos;
    }

    /**
     * Generate invoice_products record from a collection of invoice product dtos
     * @param Collection $invoiceProductDTOs
     * @return void
     */
    public function generateInvoiceProductRecords(Collection $invoiceProductDTOs): void
    {
        // create invoice product records from dto
        $invoiceProductDTOs->each(function (InvoiceProductDto $invoiceProductDto) {
            $invoiceProductDto->invoice()->products()->create([
                'product_id' => $invoiceProductDto->product()->product_id,
                'product_qty' => $invoiceProductDto->productQty(),
                'product_unit_price' => $invoiceProductDto->productUnitPrice()
            ]);
        });
    }

    public function generateInvoicePdfFromDtos(Collection $invoiceProductDtos)
    {
        $invoiceTotal = 0;
        $productTotals = [];
        $invoiceProductDtos->each(function (InvoiceProductDto $invoiceProductDto) use (&$invoiceTotal, &$productTotals) {
            // calculate total price
            $invoiceTotal += ($invoiceProductDto->productQty() * $invoiceProductDto->productUnitPrice());

            // check if product already exists
            if (!isset($productTotals[$invoiceProductDto->product()->product_id])) {
                // if product doesn't exist yet
                $productTotals[$invoiceProductDto->product()->product_id]['total_quantity'] = $invoiceProductDto->productQty();
                $productTotals[$invoiceProductDto->product()->product_id]['unit_price'] = $invoiceProductDto->productUnitPrice();
                $productTotals[$invoiceProductDto->product()->product_id]['product_id'] = $invoiceProductDto->product()->product_id;
                $productTotals[$invoiceProductDto->product()->product_id]['product_name'] = $invoiceProductDto->product()->product_name;
                $productTotals[$invoiceProductDto->product()->product_id]['product_weight_g'] = $invoiceProductDto->product()->product_weight_g;
            } else {
                $productTotals[$invoiceProductDto->product()->product_id]['total_quantity'] += $invoiceProductDto->productQty();
            }
        });

        // update total on invoice record
        $invoiceProductDtos->first()->invoice()->update([
            'invoice_total' => $invoiceTotal
        ]);

        return $this->generateInvoicePdf([
            'fromBulk' => true,
            'customer' => $invoiceProductDtos->first()->invoice()->customer,
            'products' => $productTotals,
            'totalPrice' => $invoiceTotal,
            'invoice' => $invoiceProductDtos->first()->invoice()
        ]);
    }

}
