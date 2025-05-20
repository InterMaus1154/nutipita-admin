<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Component;

class ProductSelector extends Component
{
    public Collection $customers;
    public $customer_id = 0;

    public function mount()
    {
        $this->customers = Customer::select(['customer_id', 'customer_name'])->get();
    }

    public function render()
    {
        $selectedCustomer = Customer::firstWhere('customer_id', $this->customer_id);

        $products = Product::with('customPrices')->get()->map(function ($product) use ($selectedCustomer) {
            if (!$selectedCustomer) {
                return $product;
            }
            return $product->setCurrentCustomer($selectedCustomer);
        });
        return view('livewire.product-selector', compact('selectedCustomer', 'products'));
    }
}
