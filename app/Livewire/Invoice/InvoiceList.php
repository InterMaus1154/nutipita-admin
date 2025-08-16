<?php

namespace App\Livewire\Invoice;

use App\Models\Invoice;
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

    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // --- Sorting
    public string $sortField = "invoice_number";
    public string $sortDirection = "desc";
    // ----

    public array $filters = [
        'customer_id' => null
    ];

    #[On('update-filter')]
    public function applyFilter(array $filters): void
    {
        $this->resetPage();
        $this->filters = array_merge($this->filters, $filters);
    }


    /*
     * Mark an invoice status "paid"
     */
    public function markPaid(Invoice $invoice): void
    {
        if (!auth()->check()) {
            abort(403);
        }
        $invoice->update([
            'invoice_status' => 'paid'
        ]);
    }

    /*
     * Mark an invoice status "due"
     */
    public function markDue(Invoice $invoice): void
    {
        if (!auth()->check()) {
            abort(403);
        }
        $invoice->update([
            'invoice_status' => 'due'
        ]);
    }

    /*
     * Delete an invoice
     */
    public function delete(Invoice $invoice): void
    {
        if (!auth()->check()) {
            abort(403);
        }
        DB::beginTransaction();
        try {
            Storage::disk('local')->delete($invoice->invoice_path);
            $invoice->delete();
            DB::commit();
            session()->flash('success', "Invoice {$invoice->invoice_number} successfully deleted");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at deleting invoice. Check logs for more info.');
        }

    }

    /**
     * Change sorted by field
     * @param string $field
     * @return void
     */
    public function setSort(string $field): void
    {
        if($field !== $this->sortField) {
            $this->sortField = $field;
        }
        $this->sortDirection = $this->sortDirection === 'desc' ? 'asc' : 'desc';
        $this->resetPage();
    }

    public function render(): View
    {
        $filters = $this->filters;

        $query = Invoice::query()
            ->when(!empty($filters['customer_id']), function (Builder $builder) use ($filters) {
                return $builder->where('invoices.customer_id', $filters['customer_id']);
            })
            ->when(!empty($filters['invoice_status']), function (Builder $builder) use ($filters) {
                return $builder->where('invoice_status', $filters['invoice_status']);
            })
            ->with('customer:customer_id,customer_name');

        if($this->sortField === 'customer'){
            $query
                ->join('customers', 'customers.customer_id', '=', 'invoices.customer_id')
                ->orderBy('customers.customer_name', $this->sortDirection)
                ->select('invoices.*');
        }else{
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $invoices = $query->paginate(15);
        return view('livewire.invoice.invoice-list', [
            'invoices' => $invoices
        ]);
    }
}
