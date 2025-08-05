<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
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

    public int $totalIncome = 0;

    public array $productTotals = [];
    public int $totalPita = 0;

    public function mount(Collection $orders, Collection|null $products = null, bool $visibleByDefault = false, bool $withIncome = false)
    {
        $this->orders = $orders;

        if (is_null($products)) {
            $this->products = Product::select(['product_id', 'product_name'])->get();
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
            // calculate total income for all orders
            $this->totalIncome += $order->total_price;

            $this->totalPita += $order->total_pita;

            // calculate product quantity total for each product
            foreach ($this->products as $product) {
                if(isset($this->productTotals[$product->product_id])){
                    $this->productTotals[$product->product_id]['qty'] += $order->getTotalOfProduct($product);
                }else{
                    $this->productTotals[$product->product_id]['qty'] = $order->getTotalOfProduct($product);
                    $this->productTotals[$product->product_id]['name'] = $product->product_name;
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

    public function render()
    {
        return view('livewire.order-summary');
    }
}
