<?php

namespace App\Livewire\Order;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderPopupCard extends Component
{
    public ?int $orderId = null;

    private OrderService $orderService;

    public function boot(OrderService $orderService): void
    {
        $this->orderService = $orderService;
    }

    public function createInvoice(): void
    {
        if (is_null($this->orderId)) {
            return;
        }
        $this->redirect(route('invoices.create-single',
            ['order' => $this->orderId]
        ));
    }

    public function deleteOrder(): void
    {
        if (is_null($this->orderId)) {
            return;
        }

        $this->orderService->deleteOrder($this->orderId);
        $this->orderId = null;
        $this->dispatch('modal-clear');
        $this->dispatch('refresh');
    }

    public function editOrder(): void
    {
        if (is_null($this->orderId)) {
            return;
        }

        $this->dispatch('modal-open', 'modal.order-edit', ['order_id' => $this->orderId]);
    }

    #[On('refresh')]
    public function refresh(): void
    {

    }

    public function render(): View
    {
        $order = Order::query()
            ->where('order_id', $this->orderId)
            ->with('products', 'customer', 'invoice')
            ->first();

        return view('livewire.order.order-popup-card', compact('order'));
    }
}
