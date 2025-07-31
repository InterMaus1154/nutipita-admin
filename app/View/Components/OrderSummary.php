<?php

namespace App\View\Components;

use App\Models\Product;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class OrderSummary extends Component
{

    /**
     * Create a new component instance.
     */
    public function __construct(public $orders, public bool $withIncome = false, public $products = null)
    {
        if(is_null($this->products)){
            $this->products = Product::select(['product_id', 'product_name'])->get();
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.order-summary');
    }
}
