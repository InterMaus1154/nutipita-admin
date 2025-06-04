<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;

class InvoiceService
{

    public function generateInvoice(Customer|int|string $customer,
                                    string              $invoiceFrom, string $invoiceTo,
                                    ?string             $invoiceNumber = null, ?string $issueDate = null, ?string $dueDate = null, ?string $invoiceStatus = 'due'): Invoice
    {
        // if id is provided
        if (is_int($customer) || is_string($customer)) {
            $customer = Customer::find($customer);
        }

        // check if the invoice number is provided and if it is a non-duplicate
        if (isset($invoiceNumber) && Invoice::where('invoice_number', $invoiceNumber)->exists()) {
            abort(400, "Invoice number is already taken!");
        }

        $invoiceNum = $invoiceNumber ?? Invoice::generateInvoiceNumber();
        $invoiceName = 'INV-'. $invoiceNum . '.pdf';

        return $customer->invoices()->create([
            'invoice_number' => $invoiceNum,
            'invoice_issue_date' => $issueDate ?? now()->toDateString(),
            'invoice_due_date' => $dueDate ?? now()->addDay()->toDateString(),
            'invoice_from' => $invoiceFrom,
            'invoice_to' => $invoiceTo,
            'invoice_status' => $invoiceStatus,
            'invoice_name' => $invoiceName,
            'invoice_path' => 'invoices/' . $invoiceName
        ]);
    }

    /**
     * @param Collection $orders
     * @param Invoice $invoice
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateInvoiceDocument(Collection $orders, Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $totalPrice = 0;
        $allProducts = collect();
        $customer = $invoice->customer;
        foreach ($orders as $order) {
            $totalPrice += $order->total_price;
            $allProducts = $allProducts->merge($order->products);
        }

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
        return Pdf::loadView('pdf.invoice', [
            'fromBulk' => true,
            'customer' => $customer,
            'products' => $groupedProducts,
            'totalPrice' => $totalPrice,
            'invoice' => $invoice
        ])
            ->setPaper('a4', 'portrait');
    }
}
