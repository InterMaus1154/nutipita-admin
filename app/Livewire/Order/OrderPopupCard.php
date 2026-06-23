<?php

namespace App\Livewire\Order;

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderPopupCard extends Component
{

    public ?int $orderId = null;
    public bool $visible = false;

    #[On('show-order-popup')]
    public function showOrder(int|null $orderId)
    {
        $this->visible = true;
        $this->orderId = $orderId;

        $this->dispatch('open-detail-popup');
    }

    public function render()
    {
        if(is_null($this->orderId)){
            $this->visible = false;
            return view('livewire.order.order-popup-card');
        }

        $order = Order::query()
            ->where('order_id', $this->orderId)
            ->with('products', 'customer', 'invoice')
            ->first();

        return view('livewire.order.order-popup-card', compact('order'));
    }
}
