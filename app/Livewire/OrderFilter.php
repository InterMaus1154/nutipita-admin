<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Traits\HasQuickDueFilter;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class OrderFilter extends Component
{
    use HasQuickDueFilter;

    public int|string|null $customer_id = null;
    public ?string $status = null;
    public ?int $month = null;
    public bool $cancelled_order_hidden = true;
    public bool $daytime_only = false;



    public function updated(): void
    {
        $this->dispatchEvent();
    }

    public function mount(): void
    {
        /*set week as default*/
        $this->setWeek();
    }

    /**
     * Toggle
     * @return void
     */
    public function toggleDaytime(): void
    {
        $this->daytime_only = !$this->daytime_only;
        $this->dispatchEvent();
    }

    /*
     * Listens for month update
     */
    public function updatedMonth($value): void
    {
        if(empty($value)){
            $this->setMonth();
        }else{
            $year = now()->year;
            $this->due_from = now()->setDate($year, $value, 1)->startOfMonth()->toDateString();
            $this->due_to = now()->setDate($year, $value, 1)->endOfMonth()->toDateString();
            $this->activePeriod = "month";
            $this->dispatchEvent();
        }
    }

    public function dispatchEvent(): void
    {
        $this->dispatch('update-filter', [
            'customer_id' => $this->customer_id,
            'due_from' => $this->due_from,
            'due_to' => $this->due_to,
            'status' => $this->status,
            'cancelled_order_hidden' => $this->cancelled_order_hidden,
            'month' => $this->month,
            'daytime_only' => $this->daytime_only
        ]);
    }

    public function clearFilter(): void
    {
        $this->reset(['customer_id', 'daytime_only', 'status', 'month', 'due_from', 'due_to', 'cancelled_order_hidden', 'activePeriod']);
        $this->dispatchEvent();
    }

    public function render(): View
    {
        $customers = Customer::select(['customer_id', 'customer_name', 'customer_business_owner_name'])->get();
        return view('livewire.order-filter', compact('customers'));
    }
}
