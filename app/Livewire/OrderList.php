<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{

    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $filters = [
        'customer_id' => null,
        'due_from' => null,
        'due_to' => null,
        'status' => null,
        'cancelled_order_hidden' => true,
        'daytime_only' => false
    ];

    public bool $isStatusUpdateModalVisible = false;

    public ?Order $modalSelectedOrder = null;
    public ?string $updateOrderStatusName = null;
    public bool $withSummaryData = true;
    public bool $withSummaryPdf = false;
    public bool $summaryVisibleByDefault = false;

    public $ordersAll = [];
    public $orderIds = [];

    public function mount(bool $withSummaryData = true, bool $summaryVisibleByDefault = false, array $filters = [], bool $withSummaryPdf = false)
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
    }

    public function updatedUpdateOrderStatusName($value)
    {
        if($this->modalSelectedOrder){
            $this->modalSelectedOrder->update([
               'order_status' => $value
            ]);
        }
        $this->modalSelectedOrder = null;
        $this->isStatusUpdateModalVisible = false;
    }

    #[On('update-filter')]
    public function applyFilter(array $filters)
    {
        $this->resetPage();
        $this->filters = array_merge($this->filters, $filters);
    }

    public function openStatusUpdateModal(Order $order)
    {
        $this->modalSelectedOrder = $order;
        $this->isStatusUpdateModalVisible = true;
    }

    public function closeStatusUpdateModal()
    {
        $this->modalSelectedOrder = null;
        $this->isStatusUpdateModalVisible = false;
    }

    public function getOrderSummaryPdfUrl(): string
    {
        return route('orders.create-summary-pdf', ['orderIds' => $this->orderIds]);
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

        $this->ordersAll = $query->nonCancelled()->get() ; // cancelled or invalidated orders will not contribute to the summaries

        // only if pdf save is required, otherwise useless data
        if($this->withSummaryPdf){
            $this->orderIds = $this->ordersAll->pluck('order_id')->toArray();
        }

        $orders = (clone $query)
            ->paginate(15);

        $products = Product::select(['product_id', 'product_name', 'product_weight_g'])->get();
        return view('livewire.order-list', [
            'products' => $products,
            'orders' => $orders,
            'withSummaries' => true,
            'withIncome' => true
        ]);
    }
}

