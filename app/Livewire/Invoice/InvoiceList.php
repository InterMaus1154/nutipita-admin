<?php

namespace App\Livewire\Invoice;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Enums\InvoiceStatus;
use App\Domain\Invoice\Querying\InvoiceFilters;
use App\Domain\Invoice\Services\InvoiceService;
use App\Enums\OrderStatus;
use App\Helpers\SortOrder;
use App\Models\Order;
use App\Traits\HasSort;
use Detection\MobileDetect;
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

    private InvoiceRepository $invoices;
    private InvoiceService $invoiceService;

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

    public function boot(InvoiceRepository $invoices, InvoiceService $invoiceService): void
    {
        $this->invoices = $invoices;
        $this->invoiceService = $invoiceService;
    }

    public function updateInvoiceStatus(int $newValue, Invoice $invoice): void
    {
        if (!auth()->check()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            if ($newValue === InvoiceStatus::UNPAID->value) {
                Order::forInvoice($invoice)->markUnpaid();
            } else if ($newValue === InvoiceStatus::PAID->value) {
                Order::forInvoice($invoice)->markPaid();
            }

            $invoice->update([
                'invoice_status_new' => $newValue
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }
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
                'invoice_status_new' => InvoiceStatus::PAID->value,
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
                'invoice_status_new' => InvoiceStatus::UNPAID->value,
            ]);

            Order::forInvoice($invoice)->markUnpaid();

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at updating invoice or orders');
        }
    }

    public function delete(int $id): void
    {
        if (!auth()->check()) {
            abort(401);
        }

        try {
            $this->invoiceService->delete($id);
            session()->flash('success', "Invoice successfully deleted");
        } catch (\Throwable $e) {
            session()->flash('error', 'Error at deleting invoice. Check logs for more info.');
        }
    }

    #[On('refresh')]
    public function refresh(): void
    {

    }

    public function render(): View
    {
        $filters = $this->filters;

        $invoiceQuery = InvoiceFilters::from($filters);

        $invoiceTotals = $this->invoices->getInvoiceTotals($invoiceQuery);
        $invoiceCount = $this->invoices->getInvoiceCount($invoiceQuery);

        $unpaidSum = $this->invoices->allUnpaidSum();

        $invoiceQuery->sortBy($this->sortField, SortOrder::from($this->sortDirection));

        $invoices = $this->invoices->paginate(query: $invoiceQuery);
        return view('livewire.invoice.invoice-list', [
            'invoices' => $invoices,
            'invoiceTotals' => $invoiceTotals,
            'invoiceCount' => $invoiceCount,
            'unpaidSum' => $unpaidSum,
        ]);
    }
}
