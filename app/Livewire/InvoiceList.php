<?php

namespace App\Livewire;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceList extends Component
{

    use WithPagination;

    public array $filters = [
        'customer_id' => null
    ];

    #[On('update-filter')]
    public function applyFilter(array $filters): void
    {
        $this->resetPage();
        $this->filters = array_merge($this->filters, $filters);
    }

    protected $paginationTheme = 'tailwind';

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

    public function render()
    {
        $filters = $this->filters;

        $query = Invoice::query()
            ->when($filters['customer_id'], function (Builder $builder) use ($filters) {
                return $builder->where('customer_id', $filters['customer_id']);
            })
            ->with('customer:customer_id,customer_name')
            ->orderByDesc('invoice_number');

        $invoices = $query->paginate(15);
        return view('livewire.invoice-list', compact('invoices'));
    }
}
