<?php

namespace App\Livewire\Modal;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
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


    public function save(): void
    {

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
