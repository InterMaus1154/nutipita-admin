<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\View\View;
use Livewire\Component;

class CreateInvoiceFromOrder extends Component
{
    public $customers;
    public $products;

    public ?int $customer_id = null;
    public ?string $due_from = null;
    public ?string $due_to = null;

    public $orders = [];

    public function mount(): void
    {
        // init that don't change throughout cycle
        $this->customers = Customer::select(['customer_id', 'customer_name'])->get();
        $this->products = Product::select(['product_id', 'product_name'])->get();

    }

    // generate invoice
    public function submit(InvoiceService $invoiceService)
    {
        $this->validate([
            'customer_id' => 'required',
            'due_from' => 'nullable',
            'due_to' => 'nullable'
        ], [
            'customer_id.required' => 'Select a customer first!'
        ]);

        $invoice = $invoiceService->generateInvoice(
            customer: $this->customer_id,
            invoiceFrom: $this->due_from,
            invoiceTo: $this->due_to);
        $invoicePdf = $invoiceService->generateInvoiceDocument($this->orders, $invoice);
        $invoicePdf->save($invoice->invoice_path, 'local');

    }

    public function updated()
    {
        // reload order info on each updated field
        $this->loadOrderData();
    }

    private function loadOrderData()
    {
        // get orders for the selected customer
        if (isset($this->customer_id)) {
            $this->orders = Order::query()
                ->with('products', 'customer:customer_id,customer_name')
                ->nonCancelled()
                ->where('customer_id', $this->customer_id)
                ->when($this->due_from, function ($q) {
                    return $q->whereDate('order_due_at', '>=', $this->due_from);
                })
                ->when($this->due_to, function ($q) {
                    return $q->wheredate('order_due_at', '<=', $this->due_to);
                })
                ->get();
        }
    }

    public function render(): View
    {
        return view('livewire.create-invoice-from-order', [
            'orders' => $this->orders
        ]);
    }
}
