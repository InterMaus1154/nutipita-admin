<?php

namespace App\Console\Commands;

use App\Models\StandingOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckForStandingOrdersToBeActivated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-standing-order-status';

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
        info("---CheckForStandingOrdersToBeActivated running at ".now()."---");
        info("Activating standing orders running at ".now());

        try{
            $orders = StandingOrder::query()
                ->where('is_active', false)
                ->whereDate('start_from', '=', today())
                ->whereNull('is_forced')
                ->get();
            foreach ($orders as $order) {
                $order->update([
                    'is_active' => true
                ]);
            }
            info("Standing orders activated successfully! ".now());
        }catch(\Exception $e){
            Log::error($e->getMessage());
            info("Error at activating standing orders");
        }

    }
}
