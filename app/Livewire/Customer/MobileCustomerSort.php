<?php

namespace NutiPita\Livewire\Customer;

use Illuminate\View\View;
use Livewire\Component;
use NutiPita\Livewire\Customer\CustomerList;


class MobileCustomerSort extends Component
{
    public function setMobileSort(string $value): void
    {
        $this->dispatch('set-mobile-sort', [
            'value' => $value
        ])->to(CustomerList::class);
    }

    public function render(): View
    {
        return view('livewire.customer.mobile-customer-sort');
    }
}
