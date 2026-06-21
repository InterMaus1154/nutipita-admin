<?php

namespace App\Providers;

use Detection\MobileDetect;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());

        require_once app_path('Helpers/helpers.php');

        $browser = new MobileDetect;

        View::share('browser', $browser);

        Blade::directive('dayDate', function($expression){
            return "<?php echo dayDate($expression); ?>";
        });

        Blade::directive("moneyFormat", function($expression){
            return "<?php echo moneyFormat($expression); ?>";
        });

        Blade::directive('unitPriceFormat', function($expression){
            return "<?php echo unitPriceFormat($expression); ?>";
        });;

        Blade::directive('numberFormat', function($expression){
           return "<?php echo numberFormat($expression); ?>";
        });

        Blade::directive('amountFormat', function($expression){
            return "<?php echo amountFormat($expression); ?>";
        });
    }
}
