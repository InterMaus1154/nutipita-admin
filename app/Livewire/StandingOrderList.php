<?php

namespace App\Livewire;

use App\Models\StandingOrder;
use App\Traits\HasSort;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;

class StandingOrderList extends Component
{
    use HasSort;

    public function mount(): void
    {
        $this->initSort('standing_order_id');
    }


    public function updateOrderStatus(StandingOrder $order, string $value): void
    {
        DB::beginTransaction();
        try{
            $order->update([
               'is_active' => $value === "active"
            ]);
            session()->flash('success', 'Status updated');
            DB::commit();
        }catch(\Exception $e){
            DB::rollBack();
            session()->flash('error', 'Error updating status');
            Log::error($e->getMessage());
        }
    }

    public function delete(StandingOrder $order): void
    {
        try {
            DB::beginTransaction();
            foreach ($order->days as $day) {
                foreach ($day->products as $product) {
                    $product->delete();
                }
                $day->delete();
            }
            $order->delete();
            DB::commit();
            session()->flash('success', 'Standing order deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at deleting this order');
        }
    }

    public function render(): View
    {
        $ordersQuery = StandingOrder::with('customer')->select(['standing_order_id', 'customer_id', 'is_active', 'start_from']);

        $customSorts = [
            'customer' => function (Builder $query) {
                $query->join('customers', 'customers.customer_id', '=', 'standing_orders.customer_id')
                    ->orderBy('customers.customer_name', $this->sortDirection)
                    ->select('standing_orders.*');
            }
        ];

        $this->applySort($ordersQuery, $customSorts);
        $orders = $ordersQuery->get();

        return view('livewire.standing-order-list', compact('orders'));
    }
}
