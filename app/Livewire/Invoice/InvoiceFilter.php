<?php

namespace App\Livewire\Invoice;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Component;

class InvoiceFilter extends Component
{

    // --- Filter data ---
    public $customer_id = null;
    public ?string $invoice_status = null;
    // ---

    public Collection $customers;

    public function mount(): void
    {
        $this->customers = Customer::query()->select(['customer_id', 'customer_name'])->orderBy('customer_name')->get();
    }

    /**
     * Dispatch an event with updated filter options
     * @return void
     */
    public function dispatchEvent(): void
    {
        $this->dispatch('update-filter', [
            'customer_id' => $this->customer_id,
            'invoice_status' => $this->invoice_status
        ])->to(InvoiceList::class);

    }

    public function updated(): void
    {
        $this->dispatchEvent();
    }

    /**
     * Reset filter values to default
     * @return void
     */
    public function clearFilter(): void
    {
        $this->resetExcept('customers');
        $this->dispatchEvent();
    }

    public function render(): View
    {
        return view('livewire.invoice.invoice-filter');
    }
}
