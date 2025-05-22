<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\CustomerProductPrice;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class CustomerCustomPrices extends Component
{
    public Customer $customer;

    // customer coming from inserting the component
    public function mount(Customer $customer)
    {
        $this->customer = $customer;
        // TODO: optimise later (if possible)
    }

    public function delete(string $encryptedId)
    {
        // decrypt ID
        $customer_product_price_id = Crypt::decrypt($encryptedId);

        $customPrice = CustomerProductPrice::findOrFail($customer_product_price_id);

        // check ownership
        if ($this->customer->customer_id != $customPrice->customer_id) {
            abort(403);
        }

        // delete custom price
        $customPrice->delete();
    }

    public function render()
    {
        // load relationships if not loaded
        $this->customer->loadMissing('customPrices', 'customPrices.product');
        $hasCustomPrices = collect($this->customer->customPrices)->isNotEmpty();
        return view('livewire.customer-custom-prices', compact('hasCustomPrices'));
    }
}
