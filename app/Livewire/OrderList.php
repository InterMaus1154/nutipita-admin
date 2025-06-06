<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderList extends Component
{
    public Collection $orders;
    private Builder $query;

    public bool $isOnIndex = true;

    public function mount()
    {
        $this->query = Order::query();
        $this->loadOrders();

        $this->isOnIndex = Route::is('orders.index');
    }

    public function delete(int $order_id)
    {
        $order = Order::find($order_id);
        if ($order) {
            $order->products()->detach();
            $order->delete();
        }

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
            ->when($filters['due_from'], function ($builder) use ($filters) {
                return $builder->whereDate('order_due_at', '>=', $filters['due_from']);
            })
            ->when($filters['due_to'], function ($builder) use ($filters) {
                return $builder->whereDate('order_due_at', '<=', $filters['due_to']);
            })
            ->when($filters['status'], function ($builder) use ($filters) {
                return $builder->where('order_status', $filters['status']);
            });

        $this->loadOrders();
    }

    public function loadOrders()
    {
        $this->orders = $this->query
            ->with('customer:customer_id,customer_name', 'products')
            ->select(['order_status', 'order_placed_at', 'order_due_at', 'customer_id', 'order_id', 'created_at', 'is_standing'])
            ->orderByDesc('order_placed_at')
            ->get();
    }

    public function render()
    {
        $products = Product::select(['product_id', 'product_name'])->get();
        return view('livewire.order-list', [
            'products' => $products,
            'orders' => $this->orders,
            'onOrderIndex' => $this->isOnIndex
        ]);
    }
}
