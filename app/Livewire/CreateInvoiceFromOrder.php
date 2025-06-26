<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CreateInvoiceFromOrder extends Component
{
    use WithPagination;

    public $customers;
    public $products;

    public ?int $customer_id = null;
    public ?string $due_from = null;
    public ?string $due_to = null;
    public string $invoice_issue_date;
    public string $invoice_due_date;

    public $ordersAll = [];

    public function mount(): void
    {
        // init that don't change throughout cycle
        $this->customers = Customer::select(['customer_id', 'customer_name'])->get();
        $this->products = Product::select(['product_id', 'product_name'])->get();

        $this->invoice_issue_date = now()->toDateString();
        $this->invoice_due_date = now()->addDay()->toDateString();
    }

    // generate invoice
    public function submit(InvoiceService $invoiceService)
    {
        $this->validate([
            'customer_id' => 'required',
            'due_from' => 'nullable',
            'due_to' => 'nullable',
            'invoice_issue_date' => 'required|date',
            'invoice_due_date' => 'required|date'
        ], [
            'customer_id.required' => 'Select a customer first!'
        ]);

        DB::beginTransaction();
        try {
            $invoice = $invoiceService->generateInvoice(
                customer: $this->customer_id,
                invoiceFrom: $this->due_from ?? null,
                invoiceTo: $this->due_to ?? null,
                issueDate: $this->invoice_issue_date,
                dueDate: $this->invoice_due_date);
            $invoicePdf = $invoiceService->generateInvoiceDocumentFromOrders(collect($this->ordersAll), $invoice);
            $invoicePdf->save($invoice->invoice_path, 'local');
            session()->flash('success', 'Invoice created successfully!');
            session()->flash('invoice', $invoice);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at creating invoice. Check log for more info!');
        }
    }

    public function updated(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $orders = collect();
        // get orders for the selected customer
        if (isset($this->customer_id)) {
            $orderQuery = Order::query()
                ->with('products', 'customer:customer_id,customer_name')
                ->nonCancelled()
                ->where('customer_id', $this->customer_id)
                ->when($this->due_from, function ($q) {
                    return $q->whereDate('order_due_at', '>=', $this->due_from);
                })
                ->when($this->due_to, function ($q) {
                    return $q->wheredate('order_due_at', '<=', $this->due_to);
                });
            // for invoice generation, it needs all the data, but we don't need to display all
            $this->ordersAll = $orderQuery->get();
            $orders = $orderQuery->paginate(15);
        }

        return view('livewire.create-invoice-from-order', [
            'orders' => $orders
        ]);
    }
}
