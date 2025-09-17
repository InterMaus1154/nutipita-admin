<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use App\Traits\HasSort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class OrderList extends Component
{

    use WithPagination, HasSort;

    protected $paginationTheme = 'tailwind';

    public array $defaultFilters = [
        'customer_id' => null,
        'due_from' => null,
        'due_to' => null,
        'status' => null,
        'daytime_only' => false,
        'nighttime_only' => false
    ];

    public array $filters = [];
    public array $propFilters = [];


    /*
     * --- END ---
     */

    /*
     * Variables for summary
     */
    public bool $withSummaryData = true;
    public bool $withSummaryPdf = false;
    public bool $withIncome = true;
    public bool $summaryVisibleByDefault = false;
    public $orderIds = [];
    public $ordersAll = [];
    /*
     * End
     */


    /*
     * Sorting variables
     */

    public string $mobileSort = "desc:order_id";

    public bool $withMobileSort = false;
    /*
     * End
     */


    // Initial component load
    public function mount(bool $withSummaryData = true, bool $summaryVisibleByDefault = false, ?bool $withSummaryPdf = false): void
    {
        $this->resetPage();
        $this->initSort('order_id', 'desc', 'resetPage');
        $this->withSummaryData = $withSummaryData;
        $this->filters = array_replace($this->defaultFilters, $this->propFilters);
        $this->withSummaryPdf = $withSummaryPdf;
    }


    // when any filter is received from another component
    #[On('update-filter')]
    public function applyFilter(array $filters): void
    {
        $this->resetPage();
        $this->filters = array_replace($this->defaultFilters, $filters);
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


    public function updateOrderStatus(string $value, Order $order): void
    {
        DB::beginTransaction();
        try {
            $order->update([
                'order_status' => $value
            ]);
            session()->flash('success', 'Status updated');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating status');
            Log::error($e->getMessage());
        }
    }

    /**
     * Custom order sortings
     * @return array
     */
    public function customSorts(): array
    {
        return [
            'customer' => function (Builder $query) {
                $query->join('customers', 'orders.customer_id', '=', 'customers.customer_id')
                    ->orderBy('customers.customer_name', $this->sortDirection)
                    ->select('orders.*');
            },
            'total_pita' => function (Builder $query) {
                $query->leftJoin('order_product', 'order_product.order_id', '=', 'orders.order_id')
                    ->select('orders.*')
                    ->selectRaw('SUM(order_product.product_qty) as total_pita')
                    ->groupBy('orders.order_id', 'orders.customer_id', 'orders.order_status', 'orders.is_daytime', 'orders.is_standing', 'orders.order_placed_at', 'orders.order_due_at', 'orders.created_at', 'orders.updated_at')
                    ->orderBy('total_pita', $this->sortDirection);
            },
            'total_price' => function (Builder $query) {
                $query->leftJoin('order_product', 'order_product.order_id', '=', 'orders.order_id')
                    ->select('orders.*')
                    ->selectRaw('SUM(order_product.product_qty * order_product.order_product_unit_price) as total_price')
                    ->groupBy('orders.order_id', 'orders.customer_id', 'orders.order_status', 'orders.is_daytime', 'orders.is_standing', 'orders.order_placed_at', 'orders.order_due_at', 'orders.created_at', 'orders.updated_at')
                    ->orderBy('total_price', $this->sortDirection);
            }
        ];
    }

    /**
     * Create an order query from the filters
     * @return Builder
     */
    public function buildOrderQuery(): Builder
    {
        $filters = $this->filters;
        $query = Order::query()
            ->when(!empty($filters['active_period']) && $filters['active_period'] === 'today', function (Builder $query) {
                $query->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->whereDate('orders.order_due_at', now()->toDateString())
                            ->where('orders.is_daytime', true);
                    })
                        ->orWhere(function ($q2) {
                            $q2->whereDate('orders.order_due_at', now()->addDay()->toDateString())
                                ->where('orders.is_daytime', false);
                        });
                });
            })
            ->when($filters['nighttime_only'], function ($builder) {
                return $builder->where('is_daytime', false);
            })
            ->when($filters['daytime_only'], function ($builder) {
                return $builder->where('orders.is_daytime', true);
            })
            ->when(!empty($filters['customer_id']), function ($builder) use ($filters) {
                return $builder->where('orders.customer_id', $filters['customer_id']);
            })
            ->when(!empty($filters['status']), function ($builder) use ($filters) {
                return $builder->where('order_status', $filters['status']);
            })
            ->with('customer:customer_id,customer_name', 'products');

        if (empty($filters['active_period']) || $filters['active_period'] !== 'today') {
            $query->when(!empty($filters['due_from']), function ($builder) use ($filters) {
                return $builder->whereDate('order_due_at', '>=', $filters['due_from']);
            })
                ->when(!empty($filters['due_to']), function ($builder) use ($filters) {
                    return $builder->whereDate('order_due_at', '<=', $filters['due_to']);
                });
        }

        return $this->applySort($query, $this->customSorts());
    }

    public function render(): View
    {
        $products = Product::select(['product_id', 'product_name', 'product_weight_g'])->get();
        $query = $this->buildOrderQuery();

        // clone query for pagination only, as it contains everything from the filter
        $orders = (clone $query)
            ->paginate(15);

        // for summary boxes
        $this->ordersAll = $query->get();

        $this->dispatch('order-count-details', [
            'is_nighttime' => $this->filters['nighttime_only'],
            'is_daytime' => $this->filters['daytime_only'],
            'hasOrders' => $query->exists()
        ]);

        if (!empty($this->filters['customer_id'])) {
            $this->orderIds = (clone $query)->pluck('orders.order_id')->toArray();
            // send an event to the download component with the already made download link
            $this->dispatch('order-summary-link', ['url' => $this->getOrderSummaryPdfUrl()])->to(OrderSummaryDownload::class);
        }


        return view('livewire.order-list', [
            'products' => $products,
            'orders' => $orders,
            'withSummaries' => true,
        ]);
    }
}

