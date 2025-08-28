<?php

namespace App\Livewire\StandingOrder;

use App\Livewire\StandingOrderList;
use Illuminate\View\View;
use Livewire\Component;

class MobileStandingOrderSort extends Component
{
    public function setMobileSort(string $value): void
    {
        $this->dispatch('set-mobile-sort', [
            'value' => $value
        ])->to(StandingOrderList::class);
    }

    public function render(): View
    {
        return view('livewire.standing-order.mobile-standing-order-sort');
    }
}
