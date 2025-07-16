<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
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
        require_once app_path('Helpers/helpers.php');

        Blade::directive('dayDate', function($expression){
            return "<?php echo dayDate($expression); ?>";
        });

        Blade::directive("money", function($expression){
            return "<?php echo number_format($expression, 2) ?>";
        });

    }
}
