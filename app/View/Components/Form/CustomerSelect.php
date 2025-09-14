<?php

namespace App\View\Components\Form;

use App\Models\Customer;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CustomerSelect extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public bool $hasWire = false)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $customers = Customer::query()->select('customers.customer_id', 'customers.customer_name')->get();
        return view('components.form.customer-select', compact('customers'));
    }
}
