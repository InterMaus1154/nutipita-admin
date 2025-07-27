<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\StandingOrder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
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
    public function handle(): void
    {
        // correct number for today's day
        $today = now()->isoFormat('E') - 1;

        // fetch active orders for today
        $standingOrders = StandingOrder::query()
            ->where('is_active', true)
            ->whereHas('days', function (Builder $q) use ($today) {
                return $q->where('day', $today)->whereHas('products', function (Builder $q) {
                    $q->where('product_qty', '>', 0);
                });
            })
            ->with('days', 'days.products', 'customer')
            ->get();

        // create normal order from standing orders
        foreach ($standingOrders as $standingOrder) {
            DB::beginTransaction();

            // current day from the order
            $day = $standingOrder->days->where('day', $today)->first();

            // all products from the day
            $dayProducts = $day->products;

            // get customer
            $customer = $standingOrder->customer;

            try {
                // create an order record
                $order = Order::create([
                    'customer_id' => $standingOrder->customer_id,
                    'order_placed_at' => now()->toDateString(),
                    'order_due_at' => now()->addDay()->toDateString(),
                    'order_status' => OrderStatus::Y_CONFIRMED->name,
                    'is_standing' => true
                ]);
                foreach ($dayProducts as $dayProduct) {
                    $product = $dayProduct->product->setCurrentCustomer($customer);
                    $order->products()->attach($product->product_id,[
                        'product_qty' => $dayProduct->product_qty,
                        'order_product_unit_price' => $product->price
                    ]);
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
