<?php

namespace App\Services;

use App\DataTransferObjects\InvoiceDto;
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
     * @param Collection $orders
     * @param Invoice $invoice
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateInvoiceDocumentFromOrders(Collection $orders, Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $totalPrice = 0;
        $allProducts = collect();
        $customer = $invoice->customer;
        foreach ($orders as $order) {
            $totalPrice += $order->total_price;
            $allProducts = $allProducts->merge($order->products);
        }

        $invoice->update([
            'invoice_total' => $totalPrice
        ]);

        $groupedProducts = $allProducts
            ->groupBy('product_id')
            ->map(function ($items, $productId) use ($customer) {
                $unit_price = $items->first()->setCurrentCustomer($customer)->price;
                return [
                    'product_id' => $productId,
                    'product_weight_g' => $items->first()->product_weight_g,
                    'product_name' => $items->first()->product_name,
                    'total_quantity' => $items->sum('pivot.product_qty'),
                    'unit_price' => $unit_price
                ];
            });

        return $this->generateInvoicePdf([
            'fromBulk' => true,
            'customer' => $customer,
            'products' => $groupedProducts,
            'totalPrice' => $totalPrice,
            'invoice' => $invoice
        ]);
    }

    /**
     * @param array $products
     * @param Invoice $invoice
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateInvoiceDocumentFromProducts(array $products, Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $product['unit_price'] * $product['total_quantity'];
        }
        $invoice->update([
            'invoice_total' => $totalPrice
        ]);

        $customer = $invoice->loadMissing('customer')->customer;
        return $this->generateInvoicePdf([
            'fromBulk' => true,
            'totalPrice' => $totalPrice,
            'customer' => $customer,
            'products' => $products,
            'invoice' => $invoice
        ]);
    }
}
