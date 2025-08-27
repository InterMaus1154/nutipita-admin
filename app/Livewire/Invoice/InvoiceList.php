<?php

namespace App\Livewire\Invoice;

use App\Enums\OrderStatus;
use App\Models\Invoice;
use App\Models\Order;
use App\Traits\HasSort;
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


    public function mount(): void
    {
        $this->initSort('invoice_number', 'desc', 'resetPage');
    }

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
        DB::beginTransaction();
        try{
            $invoice->update([
                'invoice_status' => 'paid'
            ]);

            // mark orders in the invoice as "paid"

            // fetch orders based on invoice from/to dates
            $orderQuery = Order::query()
                ->where('customer_id', $invoice->customer_id)
                ->whereDate('order_due_at', '>=', $invoice->invoice_from)
                ->whereDate('order_due_at', '<=', $invoice->invoice_to);

            $this->markOrdersAsPaid($orderQuery);

            DB::commit();
        }catch (\Throwable $e){
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at updating invoice or orders');
        }
    }

    /*
     * Mark an invoice status "due"
     */
    public function markDue(Invoice $invoice): void
    {
        if (!auth()->check()) {
            abort(403);
        }
        DB::beginTransaction();
        try{
            $invoice->update([
                'invoice_status' => 'due'
            ]);

            // mark orders in the invoice as "unpaid"

            // fetch orders based on invoice from/to dates
            $orderQuery = Order::query()
                ->where('customer_id', $invoice->customer_id)
                ->whereDate('order_due_at', '>=', $invoice->invoice_from)
                ->whereDate('order_due_at', '<=', $invoice->invoice_to);

            $this->markOrdersAsUnpaid($orderQuery);

            DB::commit();
        }catch (\Throwable $e){
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at updating invoice or orders');
        }
    }

    /**
     * Mark selected orders as paid
     * @param Builder $query
     * @return void
     */
    public function markOrdersAsPaid(Builder $query): void
    {
        $query->update([
            'order_status' => OrderStatus::G_PAID->name
        ]);
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

    public function markOrdersAsConfirmed(Builder $query): void
    {
        $query->update([
            'order_status' => OrderStatus::Y_CONFIRMED->name
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

            $orderQuery = Order::query()
                ->where('customer_id', $invoice->customer_id)
                ->whereDate('order_due_at', '>=', $invoice->invoice_from)
                ->whereDate('order_due_at', '<=', $invoice->invoice_to);

            $this->markOrdersAsConfirmed($orderQuery);

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
            'customer' => function(Builder $query){
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
            ->with('customer:customer_id,customer_name');

        $this->applySort($query, $this->customSorts());

        $invoices = $query->paginate(15);
        return view('livewire.invoice.invoice-list', [
            'invoices' => $invoices
        ]);
    }
}
