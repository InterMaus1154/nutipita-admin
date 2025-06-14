<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Component;

class MoneyFilter extends Component
{

    public $orders;
    public $customers;
    public int $orderCount;
    public float $totalIncome;

    public string $due_from;
    public string $due_to;
    public ?string $customer_id = null;

    public function mount()
    {

        $this->customers = Customer::select(['customer_id', 'customer_name'])->get();

        $this->totalIncome = 0;
        $this->orderCount = 0;

        $this->setWeek();
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
            ->nonCancelled()
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

    public function setToday(): void
    {
        $this->due_from = now()->addDay()->toDateString();
        $this->due_to = now()->addDay()->toDateString();

        $this->loadOrderData();
    }

    public function setWeek(): void
    {
        $this->due_from = now()->startOfWeek()->subDay()->format('Y-m-d');
        $this->due_to = now()->endOfWeek()->subDay()->format('Y-m-d');
        $this->loadOrderData();
    }

    public function setMonth(): void
    {
        $this->due_from = now()->startOfMonth()->toDateString();
        $this->due_to = now()->endOfMonth()->toDateString();
        $this->loadOrderData();
    }

    public function setYear()
    {
        $this->due_from = now()->startOfYear()->toDateString();
        $this->due_to = now()->endOfYear()->toDateString();
        $this->loadOrderData();
    }

    public function render(): View
    {
        return view('livewire.money-filter');
    }
}
