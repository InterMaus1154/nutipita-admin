<?php

namespace App\Livewire\Invoice;

use App\Domain\Invoice\Entities\DeliveryCharge;
use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Entities\ManualInvoiceProductData;
use App\Domain\Invoice\Enums\CreateInvoiceMode;
use App\Domain\Invoice\Exceptions\CannotCreateInvoiceException;
use App\Domain\Invoice\Services\InvoiceService;
use App\Domain\Invoice\Strategies\CreateInvoiceStrategyFactory;
use App\Models\Customer;
use App\Models\Product;
use App\Traits\HasQuickDueFilter;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;

class CreateInvoice extends Component
{
    use HasQuickDueFilter;

    public $customers;
    public $products;

    public CreateInvoiceMode $formMode = CreateInvoiceMode::AUTO;

    public $ordersAll = [];

    // ---
    // Form fields
    // ---
    public string $invoice_issue_date;
    public string $invoice_due_date;

    public string|null $invoice_delivery_charge = null;
    public float|null $invoice_credit = null;


    public $customer_id = null;
    public array $invoiceProducts = [];
    public string $invoice_number;
    public string $liveInvoiceTotal = '£----';
    // ---
    // End Form fields
    // ---

    public ?string $order_status = null;

    private InvoiceService $invoiceService;
    private CreateInvoiceStrategyFactory $strategies;

    public function boot(CreateInvoiceStrategyFactory $strategies, InvoiceService $invoiceService): void
    {
        $this->invoiceService = $invoiceService;
        $this->strategies = $strategies;
    }

    public function toggleMode(): void
    {
        $this->formMode = $this->formMode === CreateInvoiceMode::AUTO ? CreateInvoiceMode::MANUAL : CreateInvoiceMode::AUTO;
    }

    public function mount(): void
    {
        $this->dispatchAble = true;

        // init that don't change throughout cycle
        $this->customers = Customer::select(['customer_id', 'customer_name'])->get();
        $this->products = Product::select(['product_id', 'product_name', 'product_weight_g'])->with('customPrices')->get();

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
        if ($this->formMode == CreateInvoiceMode::AUTO) {
            if (isset($this->due_from) && isset($this->due_to) && isset($this->customer_id)) {
                $this->liveInvoiceTotal = moneyFormat((float)DB::table('orders')
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

    public function save(): void
    {
        $this->validate([
            'customer_id' => 'required|integer',
            'invoice_due_date' => 'required|date',
            'invoice_issue_date' => 'required|date',
            'due_from' => 'required|date',
            'due_to' => 'required|date',
            'invoice_number' => 'required|string|unique:invoices,invoice_number',
            'invoice_delivery_charge' => 'nullable|string',
            'invoice_credit' => 'nullable|numeric|min:0'
        ]);

        try {

            if ($this->formMode === CreateInvoiceMode::AUTO) {
                $invoice = $this->invoiceService->createRecordUsingStrategy($this->strategies->auto(
                    customerId: $this->customer_id,
                    dueDate: new Carbon($this->invoice_due_date),
                    issueDate: new Carbon($this->invoice_issue_date),
                    periodFrom: new Carbon($this->due_from),
                    periodTo: new Carbon($this->due_to),
                    invoiceNumber: $this->invoice_number,
                    deliveryCharge: DeliveryCharge::fromString($this->invoice_delivery_charge),
                    credit: $this->invoice_credit
                ));
            } else {

                $products = [];
                foreach ($this->invoiceProducts as $id => $qty) {
                    if (!is_numeric($qty) || (int)$qty <= 0) {
                        continue;
                    }
                    $products[] = new ManualInvoiceProductData(productId: $id, qty: $qty);
                }

                $invoice = $this->invoiceService->createRecordUsingStrategy($this->strategies->manual(
                    customerId: $this->customer_id,
                    dueDate: new Carbon($this->invoice_due_date),
                    issueDate: new Carbon($this->invoice_issue_date),
                    periodFrom: new Carbon($this->due_from),
                    periodTo: new Carbon($this->due_to),
                    invoiceNumber: $this->invoice_number,
                    products: $products,
                    attachOrders: true,
                    deliveryCharge: DeliveryCharge::fromString($this->invoice_delivery_charge),
                    credit: $this->invoice_credit,
                ));
            }

            session()->flash('success', 'Invoice created successfully!');
            session()->flash('invoice', $invoice);

            $this->resetInvoiceForm();
        } catch (CannotCreateInvoiceException $e) {
            session()->flash('error', $e->getMessage());
        } catch (\Throwable $e) {
            session()->flash('error', 'Unknown error when generating invoice');
            Log::error($e->getMessage(), ['exception' => $e]);
        }
    }


    /**
     * Resets form to its default state
     * @return void
     */
    public function resetInvoiceForm(): void
    {
        $this->reset('customer_id', 'invoiceProducts', 'invoice_delivery_charge');
        $this->invoice_number = Invoice::getNextInvoiceNumber();
    }


    public function hydrate(): void
    {
        $this->products->loadMissing('customPrices');
    }

    public function render(): View
    {
        return view('livewire.invoice.create-invoice');
    }
}
