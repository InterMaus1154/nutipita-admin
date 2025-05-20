<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Component;

class ProductSelector extends Component
{
    public function render()
    {
        $customers = Customer::select(['customer_id', 'customer_name'])->get();
        $products = Product::all();

        return view('livewire.product-selector', compact('customers', 'products'));
    }
}
