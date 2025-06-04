<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\View\View;
use Livewire\Component;

class CreateInvoiceFilter extends Component
{
    public $customers;
    public $products;

    public ?int $customer_id = null;
    public ?string $due_from = null;
    public ?string $due_to = null;

    private $orders = [];

    public function mount(): void
    {
        // init that don't change throughout cycle
        $this->customers = Customer::select(['customer_id', 'customer_name'])->get();
        $this->products = Product::select(['product_id', 'product_name'])->get();
    }

    public function render(): View
    {
        // get orders for the selected customer
        if (isset($this->customer_id)) {
            $this->orders = Order::query()
                ->nonCancelled()
                ->where('customer_id', $this->customer_id)
                ->when($this->due_from, function ($q) {
                    return $q->whereDate('order_due_at', '>=', $this->due_from);
                })
                ->when($this->due_to, function ($q) {
                    return $q->wheredate('order_due_at', '<=', $this->due_to);
                })
                ->get();
        }
        return view('livewire.create-invoice-filter', [
            'orders' => $this->orders
        ]);
    }
}
