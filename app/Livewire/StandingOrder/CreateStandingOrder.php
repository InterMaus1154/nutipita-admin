<?php

namespace App\Livewire\StandingOrder;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class CreateStandingOrder extends Component
{

    public ?int $customer_id = null;

    public function render()
    {

        if (!isset($this->customer_id)) {
            $products = [];
        } else {
            $products = Product::query()
                ->whereHas('customPrices', function (Builder $query){
                    $query->where('customer_id', $this->customer_id);
                })
                ->select('product_id', 'product_name', 'product_weight_g')
                ->get();
        }

        return view('livewire.standing-order.create-standing-order', compact('products'));
    }
}
