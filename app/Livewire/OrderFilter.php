<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;

class OrderFilter extends Component
{

    public ?int $customer_id = null;
    public ?string $due_from = null;
    public ?string $due_to = null;
    public ?string $status = null;

    public function updated()
    {
        $this->dispatchEvent();
    }

    public function dispatchEvent()
    {
        $this->dispatch('update-filter', [
            'customer_id' => $this->customer_id,
            'due_from' => $this->due_from,
            'due_to' => $this->due_to,
            'status' => $this->status
        ]);
    }

    public function clearFilter()
    {
        $this->reset();
        $this->dispatchEvent();
    }

    public function render()
    {
        $customers = Customer::select(['customer_id', 'customer_name'])->get();
        return view('livewire.order-filter', compact('customers'));
    }
}
