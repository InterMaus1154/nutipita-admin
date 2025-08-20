<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Traits\HasQuickDueFilter;
use Carbon\WeekDay;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class MoneyFilter extends Component
{

    use HasQuickDueFilter;

    public $orders;
    public $customers;
    public int $orderCount;
    public float $totalIncome;

    public ?string $customer_id = null;


    public function mount(): void
    {
        $this->setAfterChangeMethod('loadOrderData');
        $this->customers = Customer::select(['customer_id', 'customer_name'])->get();

        $this->totalIncome = 0;
        $this->orderCount = 0;

        $this->setCurrentWeek();
    }

    public function updated(): void
    {
        $this->loadOrderData();
    }

    public function loadOrderData(): void
    {
        $this->totalIncome = 0;
        $this->orderCount = 0;
        $this->orders = Order::query()
            ->when($this->customer_id, function ($q) {
                return $q->where('customer_id', $this->customer_id);
            })
            ->when($this->due_from, function ($q) {
                return $q->whereDate('order_due_at', '>=', $this->due_from);
            })
            ->when($this->due_to, function ($q) {
                return $q->whereDate('order_due_at', '<=', $this->due_to);
            })
            ->select(['order_id', 'customer_id'])
            ->chunk(100, function($orders){
                foreach ($orders as $order) {
                    $this->totalIncome += $order->totalPrice;
                    $this->orderCount++;
                }
            });
    }


    public function render(): View
    {
        return view('livewire.money-filter');
    }
}
