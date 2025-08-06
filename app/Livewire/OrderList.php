<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    /*
     * Variables for status update modal
     */
    public bool $isStatusUpdateModalVisible = false;

    public ?Order $modalSelectedOrder = null;
    public ?string $updateOrderStatusName = null;

    /*
     * --- END ---
     */

    /*
     * Variables for summary
     */
    public bool $withSummaryData = true;
    public bool $withSummaryPdf = false;
    public bool $summaryVisibleByDefault = false;
    public $orderIds = [];
    public $ordersAll = [];
    /*
     * End
     */

    public string $sortField = "order_placed_at";
    public string $sortDirection = "desc";


    // Initial component load
    public function mount(bool $withSummaryData = true, bool $summaryVisibleByDefault = false, array $filters = [], ?bool $withSummaryPdf = false): void
    {
        $this->resetPage();
        $this->withSummaryData = $withSummaryData;
        $this->filters = array_merge([
            'customer_id' => null,
            'due_from' => null,
            'due_to' => null,
            'status' => null,
            'cancelled_order_hidden' => true,
            'daytime_only' => false
        ], $filters);
        $this->withSummaryPdf = $withSummaryPdf;
    }

    // when any filter is received from another component
    #[On('update-filter')]
    public function applyFilter(array $filters): void
    {
        $this->resetPage();
        $this->filters = array_merge($this->filters, $filters);
    }

    /*
     * Methods for controlling status update modal
     */
    public function openStatusUpdateModal(Order $order)
    {
        $this->modalSelectedOrder = $order;
        $this->isStatusUpdateModalVisible = true;
        $this->updateOrderStatusName = $order->order_status;

    }

    public function closeStatusUpdateModal()
    {
        $this->reset(['modalSelectedOrder', 'isStatusUpdateModalVisible', 'updateOrderStatusName']);
    }

    public function updatedUpdateOrderStatusName($value)
    {
        if (!auth()->check()) {
            abort(403);
        }
        if ($this->modalSelectedOrder) {
            $this->modalSelectedOrder->update([
                'order_status' => $value
            ]);
        }
        $this->reset(['modalSelectedOrder', 'isStatusUpdateModalVisible', 'updateOrderStatusName']);
    }

    /*
     * --- END ---
     */

    // returns the route url with an array of order ids, that are to be passed on creating a summary pdf
    public function getOrderSummaryPdfUrl(): string
    {
        return route('orders.create-summary-pdf', ['orderIds' => $this->orderIds]);
    }

    public function deleteOrder(Order $order): void
    {
        if (!auth()->check()) {
            abort(401, 'You are unauthenticated!');
        }

        DB::beginTransaction();
        try {
            $order->delete();
            session()->flash('success', "Order #{$order->order_id} deleted successfully");
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            session()->flash('error', 'Error at deleting order. Check log');
            DB::rollBack();
        }
    }

    public function setSort($field): void
    {
        if ($this->sortField !== $field) {
            $this->sortField = $field;
        }
        $this->sortDirection = $this->sortDirection === "desc" ? "asc" : "desc";
        $this->resetPage();
    }

    public function render()
    {
        $filters = $this->filters;
        $query = Order::query()
            ->when($filters['cancelled_order_hidden'], function ($builder) {
                return $builder->nonCancelled();
            })
            ->when($filters['daytime_only'], function ($builder) {
                return $builder->where('is_daytime', true);
            })
            ->when(!empty($filters['customer_id']), function ($builder) use ($filters) {
                return $builder->where('orders.customer_id', $filters['customer_id']);
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
            ->with('customer:customer_id,customer_name', 'products');


        // order sorting
        if ($this->sortField === "customer") {
            // sort by customer name
            $query->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
                ->orderBy('customers.customer_name', $this->sortDirection)
                ->select('orders.*');
        } else if ($this->sortField === "total_pita") {
            // sort by the amount of total pita in an order
            $query->leftJoin('order_product', 'order_product.order_id', '=', 'orders.order_id')
                ->select('orders.*')
                ->selectRaw('SUM(order_product.product_qty) as total_pita')
                ->groupBy('orders.order_id', 'orders.customer_id', 'orders.order_status', 'orders.is_daytime', 'orders.is_standing', 'orders.order_placed_at', 'orders.order_due_at', 'orders.created_at', 'orders.updated_at')
                ->orderBy('total_pita', $this->sortDirection);
        } else if ($this->sortField === "total_price") {
            // sort by total price of an order
            $query->leftJoin('order_product', 'order_product.order_id', '=', 'orders.order_id')
                ->select('orders.*')
                ->selectRaw('SUM(order_product.product_qty * order_product.order_product_unit_price) as total_price')
                ->groupBy('orders.order_id', 'orders.customer_id', 'orders.order_status', 'orders.is_daytime', 'orders.is_standing', 'orders.order_placed_at', 'orders.order_due_at', 'orders.created_at', 'orders.updated_at')
                ->orderBy('total_price', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        // clone query for pagination only, as it contains everything from the filter
        $orders = (clone $query)
            ->paginate(15);

        // cancelled or invalidated orders will not contribute to the summaries, hence the query clone above
        $this->ordersAll = $query->nonCancelled()->get();

        // only if pdf save is required, otherwise useless data
        if ($this->withSummaryPdf) {
            $this->orderIds = $this->ordersAll->pluck('order_id')->toArray();
        }

        $products = Product::select(['product_id', 'product_name', 'product_weight_g'])->get();
        return view('livewire.order-list', [
            'products' => $products,
            'orders' => $orders,
            'withSummaries' => true,
            'withIncome' => true
        ]);
    }
}

