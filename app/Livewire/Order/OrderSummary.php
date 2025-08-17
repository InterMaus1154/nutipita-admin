<?php

namespace App\Livewire\Order;

use App\Enums\OrderStatus;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class OrderSummary extends Component
{
    #[Reactive]
    public Collection $orders;
    public Collection|null $products = null;
    public bool $visible = false;
    public bool $withIncome = false;

    public float $totalIncome = 0;

    public array $productTotals = [];
    public int $totalPita = 0;

    public function mount(Collection $orders, Collection|null $products = null, bool $visibleByDefault = false, bool $withIncome = false): void
    {
        $this->orders = $orders;

        if (is_null($products)) {
            $this->products = Product::select(['product_id', 'product_name', 'product_weight_g'])->get();
        } else {
            $this->products = $products;
        }

        $this->visible = $visibleByDefault;
        $this->withIncome = $withIncome;

        $this->calculateSummaries();
    }

    /**
     * Calculate summary details
     * @return void
     */
    #[On('update-filter')]
    public function calculateSummaries(): void
    {
        $this->reset(['totalPita', 'productTotals', 'totalIncome']);

        foreach ($this->orders as $order) {
            // only add to income, if the order has been paid
            if($order->order_status === OrderStatus::G_PAID->name){
                $this->totalIncome += $order->total_price;
            }

            $this->totalPita += $order->total_pita;

            // calculate product quantity total for each product
            foreach ($this->products as $product) {
                if(isset($this->productTotals[$product->product_id])){
                    $this->productTotals[$product->product_id]['qty'] += $order->getTotalOfProduct($product);
                }else{
                    $this->productTotals[$product->product_id]['qty'] = $order->getTotalOfProduct($product);
                    $this->productTotals[$product->product_id]['name'] = $product->product_name;
                    $this->productTotals[$product->product_id]['g'] = $product->product_weight_g;
                }
            }
        }
    }

    /**
     * Toggle the visibility of the summary boxes
     * @return void
     */
    public function toggleSummaries(): void
    {
        $this->visible = !$this->visible;
    }

    public function render(): View
    {
        return view('livewire.order.order-summary');
    }
}
