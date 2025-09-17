<?php

namespace App\Livewire\Order;

use App\Enums\OrderStatus;
use App\Livewire\OrderList;
use App\Models\Product;
use App\Queries\OrderQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class OrderSummary extends Component
{
    #[Reactive]
    public array $filters;

    #[Reactive]
    public $disabled = false;

    public bool $visible = false;
    public bool $withIncome = false;

    public float|null $totalIncome = 0;
    public int|null $ordersCount = 0;
    public $productTotals = [];
    public int|null $totalPita = 0;

    public function mount(Collection|null $products = null, bool $visibleByDefault = false, bool $withIncome = false): void
    {
        $this->visible = $visibleByDefault;
        $this->withIncome = $withIncome;
    }

    /**
     * Calculate summary details
     * @return void
     */
    public function calculateSummaries(): void
    {
        $this->reset(['totalPita', 'productTotals', 'totalIncome']);

        $query = OrderQueryBuilder::build($this->filters);

        $this->ordersCount = (clone $query)
            ->selectRaw('COUNT(*) AS total_orders')
            ->first()->total_orders;

        $this->totalIncome = (clone $query)
            ->join('order_product', 'order_product.order_id', '=', 'orders.order_id')
            ->selectRaw('SUM(order_product.order_product_unit_price * order_product.product_qty) AS total_income')
            ->first()->total_income;

        $this->productTotals = (clone $query)
            ->join('order_product', 'orders.order_id', '=', 'order_product.order_id')
            ->join('products', 'products.product_id', '=', 'order_product.product_id')
            ->select('products.product_id', 'products.product_name', 'products.product_weight_g')
            ->selectRaw('SUM(order_product.product_qty) AS product_qty')
            ->groupBy('products.product_id', 'products.product_name', 'products.product_weight_g')
            ->orderBy('products.product_name', 'DESC')
            ->get();

        $this->totalPita = (clone $query)
            ->join('order_product', 'orders.order_id', '=', 'order_product.order_id')
            ->selectRaw('SUM(order_product.product_qty) AS total_pita')
            ->getQuery()
            ->value('total_pita');

    }


    public function render(): View
    {
        if(!$this->disabled){
            $this->calculateSummaries();
        }
        return view('livewire.order.order-summary');
    }
}
