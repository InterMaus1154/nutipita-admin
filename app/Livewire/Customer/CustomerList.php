<?php

namespace App\Livewire\Customer;

use App\Models\Customer;
use App\Models\Product;
use App\Services\CustomerService;
use App\Traits\HasSort;
use Detection\MobileDetect;
use Illuminate\View\View;
use Livewire\Component;

class CustomerList extends Component
{

    use HasSort;

    public $products;
    public bool $isMobile = false;

    private CustomerService $customerService;

    public function boot(CustomerService $customerService): void
    {
        $this->customerService = $customerService;
    }

    public function mount(): void
    {

        $this->initSort('customer_name', 'asc');
        $this->products = Product::whereHas('customPrices')->get();

        $browser = new MobileDetect;
        $this->isMobile = $browser->isMobile() && !$browser->isTablet();
    }

    public function toggleCustomerVisibility(int $id): void
    {
        $customer = $this->customerService->findById($id);
        if(is_null($customer)){
            abort(404, 'Customer not found');
        }
        $customer->toggleHidden();
        $this->customerService->commit($customer);
        session()->flash('success', 'Customer updated');
    }

    public function render(): View
    {
        $customerQuery = Customer::query()
            ->with('customPrices', 'customPrices.product')
            ->select('*');

        $this->applySort($customerQuery);

        $customers = $customerQuery->get();

        return view('livewire.customer.customer-list', compact('customers'));
    }
}
