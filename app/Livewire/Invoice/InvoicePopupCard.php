<?php

namespace App\Livewire\Invoice;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\View\View;
use Livewire\Component;

class InvoicePopupCard extends Component
{
    public ?int $invoiceId = null;
    private InvoiceService $invoiceService;

    public function boot(InvoiceService $invoiceService): void
    {
        $this->invoiceService = $invoiceService;
    }

    public function deleteInvoice(): void
    {
        if(is_null($this->invoiceId)){
            return;
        }

        $this->invoiceService->deleteInvoice($this->invoiceId);
        $this->invoiceId = null;
        $this->dispatch('modal-clear');
        $this->dispatch('refresh');
    }

    public function render(): View
    {
        $invoice = Invoice::query()
            ->where('invoice_id', $this->invoiceId)
            ->with('customer:customer_id,customer_name', 'products')
            ->first();
        return view('livewire.invoice.invoice-popup-card', compact('invoice'));
    }
}
