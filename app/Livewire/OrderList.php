<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{

    use WithPagination;

    public $filters = [
        'customer_id' => null,
        'due_from' => null,
        'due_to' => null,
        'status' => null,
        'cancelled_order_hidden' => true,
        'daytime_only' => false
    ];

    public bool $isOnIndex = true;
    public bool $withSummaryData = true;
    public bool $summaryVisibleByDefault = false;

    protected $paginationTheme = 'tailwind';

    public function mount(bool $withSummaryData = true, bool $summaryVisibleByDefault = false, array $filters = [])
    {
        $this->withSummaryData = $withSummaryData;
        $this->filters = array_merge([
            'customer_id' => null,
            'due_from' => null,
            'due_to' => null,
            'status' => null,
            'cancelled_order_hidden' => true,
            'daytime_only' => false
        ], $filters);
        $this->isOnIndex = Route::is('orders.index');
    }

//    public function delete(int $order_id)
//    {
//        $order = Order::find($order_id);
//        if ($order) {
//            $order->products()->detach();
//            $order->delete();
//        }
//
//        $this->query = Order::query();
//        $this->loadOrders();
//    }

    #[On('update-filter')]
    public function applyFilter(array $filters)
    {
        $this->resetPage();
        $this->filters = array_merge($this->filters, $filters);
    }

    public function render()
    {
        $filters = $this->filters;
        $query = Order::query()
            ->when($filters['cancelled_order_hidden'], function ($builder) {
                return $builder->nonCancelled();
            })
            ->when($filters['daytime_only'], function($builder){
                return $builder->where('is_daytime', true);
            })
            ->when(!empty($filters['customer_id']), function ($builder) use ($filters) {
                return $builder->where('customer_id', $filters['customer_id']);
            })
            ->when(!empty($filters['due_from']), function ($builder) use ($filters) {
                return $builder->whereDate('order_due_at', '>=', $filters['due_from']);
            })
            ->when(!empty($filters['due_to']), function ($builder) use ($filters) {
                return $builder->whereDate('order_due_at', '<=', $filters['due_to']);
            })
            ->when(!empty($filters['status']), function ($builder) use ($filters) {
                return $builder->where('order_status', $filters['status']);
            })
            ->with('customer:customer_id,customer_name', 'products')
            ->orderByDesc('order_placed_at')
            ->orderByDesc('order_id');

        $orders = (clone $query)
            ->paginate(15);

        $products = Product::select(['product_id', 'product_name', 'product_weight_g'])->get();
        return view('livewire.order-list', [
            'products' => $products,
            'orders' => $orders,
            'onOrderIndex' => $this->isOnIndex,
            'withSummaries' => true,
            'withIncome' => true,
            'ordersAll' => $query->nonCancelled()->get() // cancelled or invalidated orders will not contribute to the summaries
        ]);
    }
}

