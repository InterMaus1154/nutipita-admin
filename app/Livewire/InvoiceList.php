<?php

namespace App\Livewire;

use App\Models\Invoice;
use Livewire\Component;

class InvoiceList extends Component
{

    /*
     * Mark an invoice status "paid"
     */
    public function markPaid(Invoice $invoice)
    {
        $invoice->update([
            'invoice_status' => 'paid'
        ]);
    }

    /*
     * Mark an invoice status "due"
     */
    public function markDue(Invoice $invoice)
    {
        $invoice->update([
            'invoice_status' => 'due'
        ]);
    }

    public function render()
    {
        $invoices = Invoice::with('customer:customer_id,customer_name')->orderByDesc('invoice_number')->get();
        return view('livewire.invoice-list', compact('invoices'));
    }
}
