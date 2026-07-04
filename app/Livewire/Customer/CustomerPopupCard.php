<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use Livewire\Component;

class CustomerPopupCard extends Component
{

    public ?int $customerId = null;

    public function mount(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function render()
    {
        $customer = Customer::query()
            ->where('customer_id', $this->customerId)
            ->with('customPrices', 'customPrices.product')
            ->first();

        if(is_null($customer)){
            abort(404);
        }

        return view('livewire.customer.customer-popup-card', compact('customer'));
    }
}
