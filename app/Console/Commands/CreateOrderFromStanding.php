<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\StandingOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateOrderFromStanding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-order-from-standing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->isoFormat('E') - 1;
        $standingOrders = StandingOrder::query()
            ->where('is_active', true)
            ->whereHas('days', function ($q) use ($today) {
                return $q->where('day', $today);
            })
            ->with('days', 'days.products', 'customer')
            ->get();
        // create normal order from standing orders
        foreach ($standingOrders as $standingOrder) {
            DB::beginTransaction();
            $day = $standingOrder->days->where('day', $today)->first();
            $dayProducts = $day->products;
            try {
                $order = Order::create([
                    'customer_id' => $standingOrder->customer_id,
                    'placed_at' => now()->toDateString(),
                    'due_at' => now()->toDateString(),
                    OrderStatus::Y_CONFIRMED->name,
                    'is_standing' => true
                ]);
                foreach ($dayProducts as $product) {

                }
                DB::commit();
                Log::info("Standing order successfully created as order " . $order->order_id);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                Log::error("Error at creating order for " . $standingOrder->standing_order_id);
                DB::rollBack();
            }
        }
    }
}
