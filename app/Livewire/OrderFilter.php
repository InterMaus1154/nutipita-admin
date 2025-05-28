<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;

class OrderFilter extends Component
{

    public ?int $customer_id;
    public ?string $due_from;
    public ?string $due_to;

    public function updated()
    {
        $this->dispatch('update-filter', [
            'customer_id' => $this->customer_id ?? null,
            'due_from' => $this->due_from ?? null,
            'due_to' => $this->due_to ?? null
        ]);
    }

    public function clearFilter()
    {
        $this->reset();
        $this->updated();
    }

    public function render()
    {
        $customers = Customer::select(['customer_id', 'customer_name'])->get();
        return view('livewire.order-filter', compact('customers'));
    }
}
