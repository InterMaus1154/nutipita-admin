<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use Livewire\Component;

class ProductSelector extends Component
{

    public ?int $customer_id = null;

    public function mount()
    {
        $this->customer_id = old('customer_id', request()->get('customer_id'));
    }

    public function render()
    {
        $customers = Customer::select(['customer_id', 'customer_name'])->get();
        if (is_null($this->customer_id)) {
            $products = [];
        } else {
            $products = Product::query()
                ->select(['product_name', 'product_id'])
                ->get()
                ->map(fn($p) => $p->setCurrentCustomer(Customer::find($this->customer_id)));
        }
        return view('livewire.product-selector', compact('customers', 'products'));
    }
}
