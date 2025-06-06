<?php

namespace App\Livewire;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class InvoiceList extends Component
{

    /*
     * Mark an invoice status "paid"
     */
    public function markPaid(Invoice $invoice): void
    {
        $invoice->update([
            'invoice_status' => 'paid'
        ]);
    }

    /*
     * Mark an invoice status "due"
     */
    public function markDue(Invoice $invoice): void
    {
        $invoice->update([
            'invoice_status' => 'due'
        ]);
    }

    /*
     * Delete an invoice
     */
    public function delete(Invoice $invoice): void
    {
        DB::beginTransaction();
        try{
            Storage::disk('local')->delete($invoice->invoice_path);
            $invoice->delete();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at deleting invoice. Check logs for more info.');
        }

    }

    public function render()
    {
        $invoices = Invoice::with('customer:customer_id,customer_name')->orderByDesc('invoice_number')->get();
        return view('livewire.invoice-list', compact('invoices'));
    }
}
