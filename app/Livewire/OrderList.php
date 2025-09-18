<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use App\Services\LivewireHelpers\OrderListService;
use App\Traits\HasSort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;
use App\Queries\OrderQueryBuilder;

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

    #[Reactive]
    public $disabled = false;

    /*
     * Variables for summary
     */
    public bool $withSummaryData = true;
    public bool $withSummaryPdf = false;
    public bool $withIncome = true;
    public bool $summaryVisibleByDefault = false;
    /*
     * End
     */


    /*
     * Sorting variables
     */
    public string $mobileSort = "desc:order_id";

    public bool $withMobileSort = false;


    // Initial component load
    public function mount(bool $withSummaryData = true, bool $summaryVisibleByDefault = false, ?bool $withSummaryPdf = false): void
    {
        $this->resetPage();
        $this->initSort('order_id', 'desc', 'resetPage');
        $this->withSummaryData = $withSummaryData;
        $this->filters = array_replace($this->defaultFilters, $this->propFilters);
        $this->withSummaryPdf = $withSummaryPdf;
    }

    #[On('update-filter')]
    public function applyFilter(array $filters): void
    {
        $this->resetPage();
        $this->filters = array_replace($this->defaultFilters, $filters);
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

    public function render(): View
    {
        $products = Product::select(['product_id', 'product_name', 'product_weight_g'])->get();

        if($this->disabled){
            $query = Order::query()->whereRaw('1 = 0');
        }else{
            $query = $this->applySort(OrderQueryBuilder::build($this->filters), OrderListService::customSorts($this->sortDirection));
        }

        // clone query for pagination only, as it contains everything from the filter
        $orders = (clone $query)
            ->paginate(15);

        $this->dispatch('order-count-details', [
            'is_nighttime' => $this->filters['nighttime_only'],
            'is_daytime' => $this->filters['daytime_only'],
            'hasOrders' => $query->exists()
        ]);

        if (!empty($this->filters['customer_id'])) {
            $orderIds = (clone $query)->pluck('orders.order_id')->toArray();
            // send an event to the download component with the already made download link
            $this->dispatch('order-summary-link', ['url' => OrderListService::getOrderSummaryPdfUrl($orderIds)])->to(OrderSummaryDownload::class);
        }

        return view('livewire.order-list', [
            'products' => $products,
            'orders' => $orders,
            'withSummaries' => true,
            'filters' => $this->filters
        ]);
    }
}

