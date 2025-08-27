<?php

namespace App\Livewire\Invoice;

use Livewire\Component;
use App\Livewire\Invoice\InvoiceList;

class MobileInvoiceSort extends Component
{

    public function setMobileSort(string $value): void
    {
        $this->dispatch('set-mobile-sort', [
            'value' => $value
        ])->to(InvoiceList::class);
    }
    public function render()
    {
        return view('livewire.invoice.mobile-invoice-sort');
    }
}
