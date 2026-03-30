<?php

namespace App\Livewire\Modal;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class OrderEdit extends Component
{

    public Order $order;

    public function mount(int|string $order_id)
    {
        $this->order = Order::find($order_id);
    }

    public function render()
    {

        $products = Product::query()
            ->whereHas('customPrices', function (Builder $q) {
                $q->where('customer_id', $this->order->customer_id);
            })
            ->select('product_name', 'product_id', 'product_weight_g')
            ->forCustomer($this->order->customer_id)
            ->get();
        return view('livewire.modal.order-edit', compact('products'));
    }
}
