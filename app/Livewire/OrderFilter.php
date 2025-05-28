<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;

class OrderFilter extends Component
{

    public ?int $customer_id;

    public function updated()
    {
        $this->dispatch('update-filter', [
            'customer_id' => $this->customer_id
        ]);
    }

    public function render()
    {
        $customers = Customer::select(['customer_id', 'customer_name'])->get();
        return view('livewire.order-filter', compact('customers'));
    }
}
