<?php

namespace NutiPita\Livewire\Order;

use NutiPita\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderPopupCard extends Component
{

    public ?int $orderId = null;

    public function render()
    {
        $order = Order::query()
            ->where('order_id', $this->orderId)
            ->with('products', 'customer', 'invoice')
            ->first();

        return view('livewire.order.order-popup-card', compact('order'));
    }
}
