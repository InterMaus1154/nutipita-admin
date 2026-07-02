<?php

namespace NutiPita\Livewire\Invoice;

use NutiPita\Models\Invoice;
use Livewire\Component;

class InvoicePopupCard extends Component
{

    public ?int $invoiceId = null;

    public function render()
    {
        $invoice = Invoice::query()
            ->where('invoice_id', $this->invoiceId)
            ->with('customer:customer_id,customer_name', 'products')
            ->first();
        return view('livewire.invoice.invoice-popup-card', compact('invoice'));
    }
}
