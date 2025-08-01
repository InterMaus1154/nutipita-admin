<?php

namespace App\Livewire;

use App\Models\Customer;
use Carbon\Carbon;
use Livewire\Component;

class OrderFilter extends Component
{

    public int|string|null $customer_id = null;
    public ?string $due_from = null;
    public ?string $due_to = null;
    public ?string $status = null;
    public ?int $month = null;
    public bool $cancelled_order_hidden = true;

    public array $months = [
        '1' => 'January',
        '2' => 'February',
        '3' => 'March',
        '4' => 'April',
        '5' => 'May',
        '6' => 'June',
        '7' => 'July',
        '8' => 'August',
        '9' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
    ];

    public function updated()
    {
        $this->dispatchEvent();
    }

    /*
     * Listens for month update
     */
    public function updatedMonth()
    {
        $year = now()->year;

        $this->due_from = now()->setDate($year, $this->month, 1)->startOfMonth()->toDateString();
        $this->due_to = now()->setDate($year, $this->month, 1)->endOfMonth()->toDateString();
        $this->dispatchEvent();
    }

    public function dispatchEvent()
    {
        $this->dispatch('update-filter', [
            'customer_id' => $this->customer_id,
            'due_from' => $this->due_from,
            'due_to' => $this->due_to,
            'status' => $this->status,
            'cancelled_order_hidden' => $this->cancelled_order_hidden,
            'month' => $this->month
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

    public function setYesterday()
    {
        $this->due_from = now()->toDateString();
        $this->due_to = now()->toDateString();
        $this->dispatchEvent();
    }

    public function render()
    {
        $customers = Customer::select(['customer_id', 'customer_name'])->get();
        return view('livewire.order-filter', compact('customers'));
    }
}
