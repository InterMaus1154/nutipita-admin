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

    public function setToday(): void
    {
        $this->due_from = now()->addDay()->toDateString();
        $this->due_to = now()->addDay()->toDateString();

        $this->dispatchEvent();
    }

    public function setWeek(): void
    {
        $this->due_from = now()->startOfWeek()->subDay()->format('Y-m-d');
        $this->due_to = now()->endOfWeek()->subDay()->format('Y-m-d');
        $this->dispatchEvent();
    }

    public function setMonth(): void
    {
        $this->due_from = now()->startOfMonth()->toDateString();
        $this->due_to = now()->endOfMonth()->toDateString();
        $this->dispatchEvent();
    }

    public function setYear()
    {
        $this->due_from = now()->startOfYear()->toDateString();
        $this->due_to = now()->endOfYear()->toDateString();
        $this->dispatchEvent();
    }

    public function render()
    {
        $customers = Customer::select(['customer_id', 'customer_name'])->get();
        return view('livewire.order-filter', compact('customers'));
    }
}
