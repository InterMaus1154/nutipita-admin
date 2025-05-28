<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderList extends Component
{
    public Collection $orders;
    private Builder $query;

    public function mount()
    {
        $this->query = Order::query();
        $this->loadOrders();
    }

    #[On('update-filter')]
    public function applyFilter(array $filters)
    {
        $this->query = Order::query();

        $this->query
            ->when($filters['customer_id'], function ($builder) use ($filters) {
                return $builder->where('customer_id', $filters['customer_id']);
            })
            ->when($filters['due_from'], function($builder) use($filters){
                return $builder->whereDate('order_due_at', '>=', $filters['due_from']);
            })
            ->when($filters['due_to'], function($builder) use($filters){
                return $builder->whereDate('order_due_at', '<=', $filters['due_to']);
            });

        $this->loadOrders();
    }

    public function loadOrders()
    {
        $this->orders = $this->query
            ->with('customer:customer_id,customer_name', 'products')
            ->select(['order_status', 'order_placed_at', 'order_due_at', 'customer_id', 'order_id', 'created_at'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function render()
    {
        $products = Product::select(['product_id', 'product_name'])->get();
        return view('livewire.order-list', [
            'products' => $products,
            'orders' => $this->orders
        ]);
    }
}
