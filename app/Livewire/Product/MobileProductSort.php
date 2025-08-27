<?php

namespace App\Livewire\Product;

use Illuminate\View\View;
use Livewire\Component;

class MobileProductSort extends Component
{

    public function setMobileSort(string $value): void
    {
        $this->dispatch('set-mobile-sort', [
            'value' => $value
        ])->to(ProductList::class);
    }

    public function render(): View
    {
        return view('livewire.product.mobile-product-sort');
    }
}
