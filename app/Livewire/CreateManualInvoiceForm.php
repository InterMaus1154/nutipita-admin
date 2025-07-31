<?php

namespace App\Livewire;

use App\DataTransferObjects\InvoiceDto;
use App\DataTransferObjects\InvoiceProductDto;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class CreateManualInvoiceForm extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $customers;
    public $products;

    // ---
    // Form fields
    // ---
    public string $invoice_issue_date;
    public string $invoice_due_date;
    public $customer_id = null;
    public $due_from = null;
    public $due_to = null;
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

    public function updating($name)
    {
        // reset pagination if the filter variables are changed
        if (in_array($name, ['customer_id', 'due_from', 'due_to'])) {
            $this->resetPage();
        }
    }


    /*
     * Save an invoice (submit form)
     */
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
            $invoiceDto = InvoiceDto::from(
                customer: $this->customer_id,
                invoiceIssueDate: $this->invoice_issue_date,
                invoiceDueDate: $this->invoice_due_date,
                invoiceOrdersFrom: $this->due_from,
                invoiceOrdersTo: $this->due_to,
                invoiceNumber: $this->invoice_number
            );
            $invoice = $invoiceService->generateInvoice($invoiceDto);

            // prepare products
            $selectedProducts = collect($this->invoiceProducts)
                ->filter(function ($qty) {
                    return $qty > 0;
                });
            // do not create invoice if all products are empty (0 qty)
            if ($selectedProducts->isEmpty()) {
                session()->flash('error', 'All products are empty!');
                return;
            }

            // create dtos from products
            $invoiceProductDtos = collect();

            collect($selectedProducts)->each(function (int $qty, int $productId) use (&$invoiceProductDtos, $invoice) {
                $product = Product::find($productId);
                $product->setCurrentCustomer($this->customer_id);
                $invoiceProductDtos->add(InvoiceProductDto::from(
                    invoice: $invoice,
                    product: $product,
                    productQty: $qty,
                    productUnitPrice: $product->price
                ));
            });

            $invoiceService->generateInvoiceProductRecords($invoiceProductDtos);

            // generate and save invoice pdf
            $invoiceService
                ->generateInvoicePdfFromDtos($invoiceProductDtos)
                ->save($invoice->invoice_path, 'local');

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
        $orders = collect();
        $totalPita = 0;
        $productTotals = collect();

        // to show orders, customer and at least one due date must be non-empty
        if (!empty($this->customer_id) && (!empty($this->due_from) || !empty($this->due_to))) {
            $orderQuery = Order::query()
                ->nonCancelled()
                ->with('customer:customer_id,customer_name', 'products')
                ->where('customer_id', $this->customer_id)
                ->when($this->due_from, function ($builder) {
                    return $builder->whereDate('order_due_at', '>=', $this->due_from);
                })
                ->when($this->due_to, function ($builder) {
                    return $builder->whereDate('order_due_at', '<=', $this->due_to);
                })
                ->select(['order_status', 'order_placed_at', 'order_due_at', 'customer_id', 'order_id', 'created_at', 'is_standing'])
                ->orderByDesc('order_id');

            $orders = $orderQuery->paginate(15);

            // calculate order totals
            foreach ($orderQuery->get() as $order) {
                $totalPita += $order->total_pita;

                foreach ($this->products as $product) {
                    if (isset($productTotals[$product->product_name])) {
                        $productTotals[$product->product_name] += $order->getTotalOfProduct($product);
                    } else {
                        $productTotals[$product->product_name] = $order->getTotalOfProduct($product);
                    }

                }
            }

        }

        return view('livewire.create-manual-invoice-form', [
            'orders' => $orders,
            'totalPita' => $totalPita,
            'productTotals' => $productTotals
        ]);
    }
}
