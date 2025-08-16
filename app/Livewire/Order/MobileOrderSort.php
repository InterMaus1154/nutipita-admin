<?php

namespace App\Livewire\Order;

use App\Livewire\OrderList;
use Illuminate\View\View;
use Livewire\Component;

class MobileOrderSort extends Component
{

    /**
     * Dispatch mobile sort
     * @param string $value
     * @return void
     */
    public function setMobileSort(string $value): void
    {
        $this->dispatch('set-mobile-sort', [
            'value' => $value
        ])->to(OrderList::class);
    }

    public function render(): View
    {
        return view('livewire.order.mobile-order-sort');
    }
}
