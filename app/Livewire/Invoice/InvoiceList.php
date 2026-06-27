<?php

namespace App\Livewire\Invoice;

use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Models\Invoice;
use App\Models\Order;
use App\Traits\HasSort;
use Detection\MobileDetect;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceList extends Component
{

    use WithPagination, HasSort;

    protected $paginationTheme = 'tailwind';

    public array $filters = [
        'customer_id' => null
    ];

    public bool $isMobile = false;

    public function mount(): void
    {
        $this->initSort('invoice_number', 'desc', 'resetPage');
        $browser = new MobileDetect;
        $this->isMobile = $browser->isMobile() && !$browser->isTablet();
    }

    public function updateInvoiceStatus(string $newValue, Invoice $invoice): void
    {
        if (!auth()->check()) {
            abort(403);
        }

        if ($newValue === InvoiceStatus::due->name) {
            Order::forInvoice($invoice)->markUnpaid();
        } else if ($newValue === InvoiceStatus::paid->name) {
            Order::forInvoice($invoice)->markPaid();
        }

        $invoice->update([
            'invoice_status' => $newValue
        ]);
    }

    #[On('update-filter')]
    public function applyFilter(array $filters): void
    {
        $this->resetPage();
        $this->filters = array_merge($this->filters, $filters);
    }

    public function markPaid(Invoice $invoice): void
    {
        if (!auth()->check()) {
            abort(403);
        }
        DB::beginTransaction();
        try {
            $invoice->update([
                'invoice_status' =>  InvoiceStatus::paid->name
            ]);

            Order::forInvoice($invoice)->markPaid();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at updating invoice or orders');
        }
    }

    public function markDue(Invoice $invoice): void
    {
        if (!auth()->check()) {
            abort(403);
        }
        DB::beginTransaction();
        try {
            $invoice->update([
                'invoice_status' => InvoiceStatus::due->name
            ]);

            Order::forInvoice($invoice)->markUnpaid();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at updating invoice or orders');
        }
    }

    public function delete(Invoice $invoice): void
    {
        if (!auth()->check()) {
            abort(403);
        }
        DB::beginTransaction();
        try {
            Storage::disk('local')->delete($invoice->invoice_path);

            if($invoice->order){
                $invoice->order->update([
                   'order_status' => OrderStatus::Y_CONFIRMED->name
                ]);
            }else{
                Order::forInvoice($invoice)->markConfirmed();
            }

            $invoice->delete();
            DB::commit();
            session()->flash('success', "Invoice {$invoice->invoice_number} successfully deleted");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at deleting invoice. Check logs for more info.');
        }

    }

    public function customSorts(): array
    {
        return [
            'customer' => function (Builder $query) {
                $query
                    ->join('customers', 'customers.customer_id', '=', 'invoices.customer_id')
                    ->orderBy('customers.customer_name', $this->sortDirection)
                    ->select('invoices.*');
            }
        ];
    }

    public function render(): View
    {
        $filters = $this->filters;

        $query = Invoice::query()
            ->when(!empty($filters['customer_id']), function (Builder $builder) use ($filters) {
                return $builder->where('invoices.customer_id', $filters['customer_id']);
            })
            ->when(!empty($filters['invoice_from']), function (Builder $builder) use ($filters) {
                return $builder->where('invoices.invoice_due_date', '>=', $filters['invoice_from']);
            })
            ->when(!empty($filters['invoice_to']), function (Builder $builder) use ($filters) {
                return $builder->where('invoices.invoice_due_date', '<=', $filters['invoice_to']);
            })
            ->with('customer:customer_id,customer_name');


        $invoiceTotals = $query->clone()->selectRaw('SUM(invoice_total) AS invoice_totals')->value('invoice_totals');
        $invoiceCount = $query->clone()->selectRaw('COUNT(*) AS invoice_count')->value('invoice_count');

        $this->applySort($query, $this->customSorts());

        $invoices = $query->paginate(15);
        return view('livewire.invoice.invoice-list', [
            'invoices' => $invoices,
            'invoiceTotals' => $invoiceTotals,
            'invoiceCount' => $invoiceCount
        ]);
    }
}
