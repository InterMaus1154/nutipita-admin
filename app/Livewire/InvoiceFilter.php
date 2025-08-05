<?php

namespace App\Livewire;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class InvoiceFilter extends Component
{

    // --- Filter data ---
    public ?string $customer_id = null;
    // ---

    public Collection $customers;

    public function mount()
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
            'customer_id' => $this->customer_id
        ]);
    }

    public function updated()
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

    public function render()
    {
        return view('livewire.invoice-filter');
    }
}
