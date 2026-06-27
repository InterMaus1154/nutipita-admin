<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use App\Services\LivewireHelpers\OrderListService;
use App\Traits\HasSort;
use Detection\MobileDetect;
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
    public ?bool $disabled = false; // when set to true, orderlist is disabled, no orders are rendered

    public bool $withSummaryData = true;
    public bool $withSummaryPdf = false;
    public bool $withIncome = true;
    public bool $summaryVisibleByDefault = false;

    public string $mobileSort = "desc:order_id";

    public bool $isMobile = false;

    // for mobile infinite scroll
    public array $mobileOrders = [];
    public bool $mobileHasMore = true;
    public bool $mobileLoading = false;
    public int $mobilePage = 1;

    private function fetchMobileOrders(): void
    {
        $query = $this->applySort(
            OrderQueryBuilder::build($this->filters), OrderListService::customSorts($this->sortDirection)
        );

        $rows = $query
            ->with('customer:customer_id,customer_name', 'invoice:invoice_id,order_id')
            ->forPage($this->mobilePage, 12)
            ->get()
            ->map(function (Order $order) {
                return [
                    'order_id' => $order->order_id,
                    'order_status' => $order->order_status,
                    'is_daytime' => $order->is_daytime,
                    'is_standing' => $order->is_standing,
                    'order_due_at' => $order->order_due_at,
                    'total_pita' => $order->total_pita,
                    'total_price' => $order->total_price,
                    'customer_name' => $order->customer->customer_name,
                    'invoice_id' => $order->invoice?->invoice_id
                ];
            })
            ->toArray();

        $this->mobileOrders = array_merge($this->mobileOrders, $rows);
        $this->mobileHasMore = count($rows) === 12;
        $this->mobileLoading = false;
    }

    public function loadMore(): void
    {
        if(!$this->mobileHasMore || $this->mobileLoading){
            return;
        }

        $this->mobileLoading = true;
        $this->mobilePage++;
        $this->fetchMobileOrders();
    }


    public function createInvoice(int $orderId): void
    {
        $this->redirect(route('invoices.create-single', [
            'order' => $orderId
        ]));
    }

    public function mount(): void
    {
        $this->resetPage();
        $this->initSort('order_due_at', 'desc', 'resetPage');
        $this->filters = array_replace($this->defaultFilters, $this->propFilters);
        $browser = new MobileDetect;
        $this->isMobile = $browser->isMobile() && !$browser->isTablet();
        if($this->isMobile){
            $this->fetchMobileOrders();
        }
    }

    #[On('update-filter')]
    public function applyFilter(array $filters): void
    {
        $this->resetPage();
        $this->filters = array_replace($this->defaultFilters, $filters);

        if($this->isMobile){
            $this->mobileOrders = [];
            $this->mobileHasMore = true;
            $this->mobileLoading = false;
            $this->mobilePage = 1;
            $this->fetchMobileOrders();
        }
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
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error updating status');
            Log::error($e->getMessage());
        }

    }

    #[On('refresh')]
    public function refresh(): void
    {
        $this->dispatch('$refresh');
    }

    public function renderOrderProducts(int $orderId): string
    {
        $order = Order::with('products')->findOrFail($orderId);
        return view('components.order.partials.order-products', compact('order'))->render();
    }

    public function render(): View
    {

        if ($this->disabled) {
            return view('livewire.order-list'); // no data is needed for the view when the list is disabled
        }

        $query = $this->applySort(OrderQueryBuilder::build($this->filters), OrderListService::customSorts($this->sortDirection));

        if ($this->isMobile) {
            return view('livewire.order-list', [
                'withSummaries' => true,
                'filters' => $this->filters,
                'orders' => collect($this->mobileOrders)
            ]);
        }

        // clone query for pagination only, as it contains everything from the filter
        $orders = (clone $query)
            ->paginate(50);

        // load only present products
        $products = $orders->flatMap(fn(Order $order) => $order->products)->unique('product_id')->values();

        $this->dispatch('order-count-details', [
            'is_nighttime' => $this->filters['nighttime_only'],
            'is_daytime' => $this->filters['daytime_only'],
            'hasOrders' => $orders->isNotEmpty()
        ]);

        if (!empty($this->filters['customer_id']) && $orders->isNotEmpty()) {
            $orderIds = (clone $query)->toBase()->pluck('orders.order_id')->toArray();
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

