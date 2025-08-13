<?php

namespace App\Livewire\Product;

use App\Models\Product;
use App\Traits\HasSort;
use Illuminate\View\View;
use Livewire\Component;

class ProductList extends Component
{

    use HasSort;

    public function mount(): void
    {
        $this->initSort('product_name', 'desc');
    }

    public function render(): View
    {
        $productQuery = Product::query()
            ->select('*');

        $this->applySort($productQuery);

        $products = $productQuery->get();

        return view('livewire.product.product-list', compact('products'));
    }
}
