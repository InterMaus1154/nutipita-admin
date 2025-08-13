<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Models\Product;
use App\Traits\HasSort;
use Illuminate\View\View;
use Livewire\Component;

class CustomerList extends Component
{

    use HasSort;

    public $products;

    public function mount(): void
    {
        $this->initSort('customer_name', 'asc');
        $this->products = Product::all();
    }

    public function render(): View
    {
        $customerQuery = Customer::query()
            ->select('*');

        $this->applySort($customerQuery);

        $customers = $customerQuery->get();

        return view('livewire.customer.customer-list', compact('customers'));
    }
}
