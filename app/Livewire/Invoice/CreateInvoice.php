<?php

namespace App\Livewire\Invoice;

use App\DataTransferObjects\InvoiceDto;
use App\DataTransferObjects\InvoiceProductDto;
use App\Enums\OrderStatus;
use App\Helpers\Format;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Services\InvoiceService;
use App\Traits\HasQuickDueFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

class CreateInvoice extends Component
{
    use HasQuickDueFilter;

    public $customers;
    public $products;

    // auto / manual
    public string $formMode = "auto";

    public $ordersAll = [];

    // ---
    // Form fields
    // ---
    public string $invoice_issue_date;
    public string $invoice_due_date;

    public float|null $invoice_delivery_charge = 0.00;

    public $customer_id = null;
    public array $invoiceProducts = [];
    public string $invoice_number;
    public string $liveInvoiceTotal = '£----';
    // ---
    // End Form fields
    // ---

    public ?string $order_status = null;

    /**
     * Toggle form mode between manual and auto
     * @return void
     */
    public function toggleMode(): void
    {
        $this->formMode = $this->formMode === "auto" ? "manual" : "auto";
    }

    public function mount(): void
    {
        $this->dispatchAble = true;

        // init that don't change throughout cycle
        $this->customers = Customer::select(['customer_id', 'customer_name'])->get();
        $this->products = Product::select(['product_id', 'product_name', 'product_weight_g'])->get();

        $this->invoice_issue_date = now()->toDateString();
        $this->invoice_due_date = now()->addDay()->toDateString();
        $this->invoice_number = Invoice::getNextInvoiceNumber();

        $this->setCurrentWeek();
        $this->year = now()->year;

        $this->setAfterChangeMethod('afterDateChangeAction');
    }

    public function updated(): void
    {
        $this->dispatchEvent();
        $this->calculateLiveInvoiceTotal();
    }

    // the method that will run after the setters are called from HasQuickDueFilter
    public function afterDateChangeAction(): void
    {
        $this->dispatchEvent();
        $this->calculateLiveInvoiceTotal();
    }

    /**
     * Calculate live total for invoice during creation
     * @return void
     */
    public function calculateLiveInvoiceTotal(): void
    {
        $this->liveInvoiceTotal = "£----";
        if ($this->formMode == 'auto') {
            if (isset($this->due_from) && isset($this->due_to) && isset($this->customer_id)) {
                $this->liveInvoiceTotal = moneyFormat((double)DB::table('orders')
                    ->where('customer_id', $this->customer_id)
                    ->whereDate('order_due_at', '>=', $this->due_from)
                    ->whereDate('order_due_at', '<=', $this->due_to)
                    ->join('order_product', 'orders.order_id', 'order_product.order_id')
                    ->selectRaw('SUM(product_qty * order_product_unit_price) AS invoice_total')
                    ->value('invoice_total'));
            }
        } else {
            if (isset($this->customer_id)) {
                $selectedProducts = collect($this->invoiceProducts)->filter(fn($qty) => $qty > 0);
                $sum = 0;
                foreach ($selectedProducts as $productId => $qty) {
                    $product = Product::find($productId);
                    $product->setCurrentCustomer($this->customer_id);
                    $sum += ($product->price * $qty);
                }
                $this->liveInvoiceTotal = moneyFormat((double)$sum);
            }
        }

    }

    public function dispatchEvent(): void
    {
        $this->dispatch('update-filter', [
            'customer_id' => $this->customer_id,
            'due_from' => $this->due_from,
            'due_to' => $this->due_to,
            'cancelled_order_hidden' => true,
            'status' => $this->order_status
        ]);
    }

    /*
     * Save an invoice (submit form)
     */
    public function save(InvoiceService $invoiceService): void
    {
        $this->validate([
            'customer_id' => 'required|integer',
            'invoice_due_date' => 'required|date',
            'invoice_issue_date' => 'required|date',
            'due_from' => 'required|date',
            'due_to' => 'required|date',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'invoice_delivery_charge' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $firstOrderDate = Order::query()
                ->where('customer_id', $this->customer_id)
                ->whereDate('order_due_at', '>=', $this->due_from)
                ->whereDate('order_due_at', '<=', $this->due_to)
                ->orderBy('order_due_at', 'asc')
                ->first()
                ->order_due_at ?? $this->due_from;

            $lastOrderDate = Order::query()
                ->where('customer_id', $this->customer_id)
                ->whereDate('order_due_at', '>=', $this->due_from)
                ->whereDate('order_due_at', '<=', $this->due_to)
                ->orderBy('order_due_at', 'desc')
                ->first()
                ->order_due_at ?? $this->due_to;

            // create invoice record
            $invoiceDto = InvoiceDto::from(
                customer: $this->customer_id,
                invoiceIssueDate: $this->invoice_issue_date,
                invoiceDueDate: $this->invoice_due_date,
                invoiceOrdersFrom: $firstOrderDate,
                invoiceOrdersTo: $lastOrderDate,
                invoiceNumber: $this->invoice_number,
                invoiceDeliveryCharge: $this->invoice_delivery_charge
            );
            $invoice = $invoiceService->generateInvoice($invoiceDto);

            // --- ON MANUAL MODE

            if ($this->formMode === "manual") {
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
            } else {
                // --- ON AUTO MODE
                // get orders for the selected customer
                if (isset($this->customer_id)) {
                    // fetch orders based on filter
                    $this->ordersAll = Order::query()
                        ->where('customer_id', $this->customer_id)
                        ->whereDate('order_due_at', '>=', $firstOrderDate)
                        ->whereDate('order_due_at', '<=', $lastOrderDate)
                        ->with('products', 'customer:customer_id,customer_name')
                        ->get();

                }

                // extract products from orders
                $products = collect();
                foreach ($this->ordersAll as $order) {
                    $products = $products->merge($order->products);
                }
                if ($products->isEmpty()) {
                    session()->flash('error', 'No products to create invoice from');
                    return;
                }
                // group products by product ids, then create invoice product dtos
                $invoiceProductDtos = $products
                    ->groupBy('product_id')
                    ->map(function (Collection $items, int $productId) use (&$invoice) {
                        $unitPrice = $items->first()->setCurrentCustomer($this->customer_id)->price;
                        $totalQty = $items->sum('pivot.product_qty');
                        return InvoiceProductDto::from(
                            invoice: $invoice,
                            product: $productId,
                            productQty: $totalQty,
                            productUnitPrice: $unitPrice
                        );
                    });
            }

            $invoiceService->generateInvoiceProductRecords($invoiceProductDtos);

            // generate and save invoice pdf
            $invoiceService
                ->generateInvoicePdfFromDtos($invoiceProductDtos)
                ->save($invoice->invoice_path, 'local');

            $orderQuery = Order::query()
                ->where('customer_id', $this->customer_id)
                ->whereDate('order_due_at', '>=', $firstOrderDate)
                ->whereDate('order_due_at', '<=', $lastOrderDate);

            $this->markOrdersAsUnpaid($orderQuery);
            session()->flash('success', 'Invoice created successfully!');
            session()->flash('invoice', $invoice);
            DB::commit();

            // reset form to default state
            $this->resetInvoiceForm();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at creating invoice. Check log for more info!');
        }
    }

    /**
     * Resets form to its default state
     * @return void
     */
    public function resetInvoiceForm(): void
    {
        $this->reset('customer_id', 'invoiceProducts');
        $this->invoice_number = Invoice::getNextInvoiceNumber();
        $this->setCurrentWeek();
    }

    /**
     * Mark selected orders as unpaid
     * @param Builder $query
     * @return void
     */
    public function markOrdersAsUnpaid(Builder $query): void
    {
        $query->update([
            'order_status' => OrderStatus::O_DELIVERED_UNPAID->name
        ]);
    }


    public function render(): View
    {
        return view('livewire.invoice.create-invoice');
    }
}
