<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateManualInvoiceForm extends Component
{
    public $customers;
    public $products;
    public $orders;

    // ---
    // Form fields
    // ---
    public string $invoice_issue_date;
    public string $invoice_due_date;
    public $customer_id = null;
    public $due_from;
    public $due_to;
    public array $invoiceProducts = [];
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

    public function updated()
    {
        if (!empty($this->customer_id) && (!empty($this->due_from) || !empty($this->due_to))) {
            $this->loadOrderData();
        } else {
            $this->orders = [];
        }

    }

    public function loadOrderData()
    {
        $this->orders = Order::query()
            ->with('customer:customer_id,customer_name', 'products')
            ->where('customer_id', $this->customer_id)
            ->when($this->due_from, function ($builder) {
                return $builder->whereDate('order_due_at', '>=', $this->due_from);
            })
            ->when($this->due_to, function ($builder) {
                return $builder->whereDate('order_due_at', '<=', $this->due_to);
            })
            ->select(['order_status', 'order_placed_at', 'order_due_at', 'customer_id', 'order_id', 'created_at', 'is_standing'])
            ->get();

    }

    public function save(InvoiceService $invoiceService)
    {
        $this->validate([
            'customer_id' => 'required|integer',
            'invoice_due_date' => 'required|date',
            'invoice_issue_date' => 'required|date',
            'due_from' => 'nullable|date',
            'due_to' => 'nullable|date',
            'invoice_number' => 'required|string|unique:invoices,invoice_number'
        ]);

        DB::beginTransaction();
        try {
            // create invoice record
            $invoice = $invoiceService->generateInvoice(
                customer: $this->customer_id,
                invoiceFrom: $this->due_from,
                invoiceTo: $this->due_to,
                invoiceNumber: $this->invoice_number,
                issueDate: $this->invoice_issue_date,
                dueDate: $this->invoice_due_date);

            // prepare products
            $selectedProducts = collect($this->invoiceProducts)
                ->filter(function ($qty) {
                    return $qty > 0;
                });
            if ($selectedProducts->isEmpty()) {
                session()->flash('error', 'All products are empty!');
                return;
            }
            $selectedProducts = $selectedProducts->map(function ($qty, $productId) {
                $product = Product::find($productId);
                $product->setCurrentCustomer($this->customer_id);
                return [
                    'product_id' => $productId,
                    'product_name' => $product->product_name,
                    'product_weight_g' => $product->product_weight_g,
                    'unit_price' => $product->price,
                    'total_quantity' => $qty
                ];
            })->values()->all();
            $pdf = $invoiceService->generateInvoiceDocumentFromProducts($selectedProducts, $invoice);
            $pdf->save($invoice->invoice_path, 'local');
            session()->flash('success', 'Invoice created successfully!');
            session()->flash('invoice', $invoice);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at creating invoice. Check log for more info!');
        }
    }


    public function render()
    {
        return view('livewire.create-manual-invoice-form', [
            'orders' => $this->orders
        ]);
    }
}
