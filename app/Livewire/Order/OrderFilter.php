<?php

namespace App\Livewire\Order;

use App\Livewire\OrderList;
use App\Models\Customer;
use App\Traits\HasQuickDueFilter;
use Illuminate\View\View;
use Livewire\Component;

class OrderFilter extends Component
{
    use HasQuickDueFilter;

    public $customer_id = null;
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
        /*set current week as default*/
        $this->setCurrentWeek();
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

    public function dispatchEvent(): void
    {
        $this->dispatch('update-filter', [
            'customer_id' => $this->customer_id,
            'due_from' => $this->due_from,
            'due_to' => $this->due_to,
            'status' => $this->status,
            'cancelled_order_hidden' => $this->cancelled_order_hidden,
            'month' => $this->month,
            'daytime_only' => $this->daytime_only,
            'active_period' => $this->activePeriod ?? ''
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
        return view('livewire.order.order-filter', compact('customers'));
    }
}
