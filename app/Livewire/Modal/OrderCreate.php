<?php

namespace App\Livewire\Modal;

use App\Livewire\OrderList;
use App\Models\Customer;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Livewire\Attributes\Validate;
use Exception;
use Livewire\Component;

class OrderCreate extends Component
{

    public string $order_due_at;
    public string $order_placed_at;
    public int|string $customer_id;
    public string $shift;

    public array $selectedProducts = [];


    public function mount(): void
    {
        $this->order_placed_at = now()->toDateString();
        $this->order_due_at = now()->addDay()->toDateString();
        $this->shift = 'night';
    }

    public function cancel(): void
    {
        $this->reset();
        $this->dispatch('modal-clear');
    }


    public function save(OrderService $orderService): void
    {
        $this->validate([
            'order_due_at' => 'required|date|after_or_equal:order_placed_at',
            'order_placed_at' => 'required|date',
            'customer_id' => 'required|exists:customers,customer_id',
            'shift' => 'required|in:day,night'
        ], [
            'customer_id.required' => 'Select a customer'
        ]);

        try {
            $orderService->createOrder(customer_id: $this->customer_id,
                order_due_at: $this->order_due_at,
                order_placed_at: $this->order_placed_at,
                products: $this->selectedProducts,
                shift: $this->shift);

            $this->dispatch('order-created')->to(OrderList::class);

        } catch (InvalidArgumentException $e) {
            $this->addError('products', $e->getMessage());
        } catch (Exception $e) {
            $this->addError('general_error', $e->getMessage());
        } finally {
            $this->dispatch('modal-clear')->to(ModalContainer::class);
        }

    }

    public function render()
    {
        $products = collect();
        if (isset($this->customer_id)) {
            $products = Product::query()
                ->whereHas('customPrices', function (Builder $q) {
                    $q->where('customer_id', $this->customer_id);
                })
                ->select('product_name', 'product_id', 'product_weight_g')
                ->forCustomer($this->customer_id)
                ->get();
        }
        return view('livewire.modal.order-create', compact('products'));
    }
}
