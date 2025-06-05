<?php

namespace App\Livewire;

use App\Models\Invoice;
use Livewire\Component;

class InvoiceList extends Component
{
    public function render()
    {
        $invoices = Invoice::with('customer:customer_id,customer_name')->orderByDesc('invoice_number')->get();
        return view('livewire.invoice-list', compact('invoices'));
    }
}
