<?php

namespace App\Livewire\Invoice;

use App\Domain\Invoice\Entities\Invoice;
use App\Domain\Invoice\Services\InvoiceService;
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

        try {
            $this->invoiceService->delete($this->invoiceId);
            session()->flash('success', "Invoice successfully deleted");
        } catch (\Throwable $e) {
            session()->flash('error', 'Error at deleting invoice. Check logs for more info.');
        }
        $this->invoiceId = null;
        $this->dispatch('modal-clear');
        $this->dispatch('refresh');
    }

    public function render(): View
    {
        $invoice = Invoice::query()
            ->where('invoice_id', $this->invoiceId)
            ->with('customer:customer_id,customer_name', 'products', 'products.product')
            ->first();
        return view('livewire.invoice.invoice-popup-card', compact('invoice'));
    }
}
