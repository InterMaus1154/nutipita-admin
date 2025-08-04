<?php

namespace App\Livewire;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceList extends Component
{

    use WithPagination;

    protected $paginationTheme = 'tailwind';

    /*
     * Mark an invoice status "paid"
     */
    public function markPaid(Invoice $invoice): void
    {
        if(!auth()->check()){
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
        if(!auth()->check()){
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
        if(!auth()->check()){
            abort(403);
        }
        DB::beginTransaction();
        try{
            Storage::disk('local')->delete($invoice->invoice_path);
            $invoice->delete();
            DB::commit();
            session()->flash('success', "Invoice {$invoice->invoice_number} successfully deleted");
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at deleting invoice. Check logs for more info.');
        }

    }

    public function render()
    {
        $invoices = Invoice::with('customer:customer_id,customer_name')->orderByDesc('invoice_number')->paginate(15);
        return view('livewire.invoice-list', compact('invoices'));
    }
}
