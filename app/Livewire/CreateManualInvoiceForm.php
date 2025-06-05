<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateManualInvoiceForm extends Component
{
    public $customers;
    public $products;

    // ---
    // Form fields
    // ---
    #[Validate('required|date')]
    public string $invoice_issue_date;
    #[Validate('required|date')]
    public string $invoice_due_date;
    #[Validate('required|integer')]
    public $customer_id = null;
    #[Validate('nullable|date')]
    public $due_from;
    #[Validate('nullable|date')]
    public $due_to;
    public array $invoiceProducts = [];
    #[Validate('required|string')]
    public string $invoice_number;
    // ---
    // End Form fields
    // ---

    public function mount()
    {
        // init that don't change throughout cycle
        $this->customers = Customer::select(['customer_id', 'customer_name'])->get();
        $this->products = Product::select(['product_id', 'product_name'])->get();

        $this->invoice_issue_date = now()->toDateString();
        $this->invoice_due_date = now()->addDay()->toDateString();
        $this->invoice_number = Invoice::generateInvoiceNumber();
    }

    public function save(InvoiceService $invoiceService)
    {
        try{
            $invoice = $invoiceService->generateInvoice(
                customer: $this->customer_id,
                invoiceFrom: $this->due_from,
                invoiceTo: $this->due_to,
                invoiceNumber: $this->invoice_number,
                issueDate: $this->invoice_issue_date,
                dueDate: $this->invoice_due_date);
        }catch (\Exception $e) {
            Log::error($e->getMessage());
            session()->flash('error', 'Error at creating invoice. Check log for more info!');
        }
    }


    public function render()
    {

        return view('livewire.create-manual-invoice-form');
    }
}
