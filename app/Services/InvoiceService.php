<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;

class InvoiceService
{

    /**
     * @param Customer|int|string $customer
     * @param string|null $invoiceFrom
     * @param string|null $invoiceTo
     * @param string|null $invoiceNumber
     * @param string|null $issueDate
     * @param string|null $dueDate
     * @param string|null $invoiceStatus
     * @return Invoice
     */
    public function generateInvoice(Customer|int|string $customer, ?string $invoiceFrom = null, ?string $invoiceTo = null, ?string $invoiceNumber = null, ?string $issueDate = null, ?string $dueDate = null, ?string $invoiceStatus = 'due'): Invoice
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
        $invoiceName = 'INV-' . $invoiceNum . '.pdf';

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
    public function generateInvoiceDocumentFromProducts(array $products, Invoice $invoice)
    {
        $totalPrice = 0;
        foreach ($products as $product){
            $totalPrice += $product['unit_price'] * $product['total_quantity'];
        }

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
