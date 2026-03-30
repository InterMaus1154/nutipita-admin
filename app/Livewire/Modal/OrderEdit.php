<?php

namespace App\Livewire\Modal;

use App\Livewire\OrderList;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use InvalidArgumentException;
use Exception;

class OrderEdit extends Component
{

    public ?Order $order = null;
    public array $fields = [];
    public array $selectedProducts = [];

    public function mount(int|string $order_id): void
    {
        $this->order = Order::find($order_id);

        $this->fields = [
            'order_placed_at' => $this->order->order_placed_at,
            'order_due_at' => $this->order->order_due_at,
            'shift' => $this->order->is_daytime ? 'day' : 'night'
        ];

        $this->selectedProducts = $this->order->products->pluck('pivot.product_qty', 'pivot.product_id')->toArray();

    }

    public function save(OrderService $orderService): void
    {
        $this->validate([
            'fields.order_due_at' => 'required|date|after_or_equal:fields.order_placed_at',
            'fields.order_placed_at' => 'required|date',
            'fields.shift' => 'required|in:day,night'
        ]);

        try{
            $orderService->updateOrder($this->order, $this->fields, $this->selectedProducts);
            $this->dispatch('refresh')->to(OrderList::class);
        }catch (InvalidArgumentException $e){
            $this->addError('products', $e->getMessage());
        }catch (Exception $e){
            $this->addError('general_error', $e->getMessage());
        }
        $this->dispatch('modal-clear')->to(ModalContainer::class);
    }

    public function cancel(): void
    {
        $this->dispatch('modal-clear');
    }

    public function render()
    {
        $customerProducts = collect();
        if (!is_null($this->order)) {
            $customerProducts = Product::query()
                ->whereHas('customPrices', function (Builder $q) {
                    $q->where('customer_id', $this->order->customer_id);
                })
                ->select('product_name', 'product_id', 'product_weight_g')
                ->forCustomer($this->order->customer_id)
                ->get();
        }
        return view('livewire.modal.order-edit', compact('customerProducts'));
    }
}
