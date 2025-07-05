<?php

namespace App\Livewire;

use App\Models\StandingOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class StandingOrderList extends Component
{

    public function delete(StandingOrder $order)
    {
        try{
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
        }catch (\Exception $e){
            DB::rollBack();
            Log::error($e->getMessage());
            session()->flash('error', 'Error at deleting this order');
        }


    }

    public function render()
    {
        $orders = StandingOrder::with('customer')->select(['standing_order_id', 'customer_id', 'is_active', 'start_from'])->get();
        return view('livewire.standing-order-list', compact('orders'));
    }
}
