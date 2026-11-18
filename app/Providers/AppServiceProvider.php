<?php

namespace App\Providers;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Order\OrderRepository;
use App\Http\Middleware\AuthMiddleware;
use App\Infrastructure\Invoice\InvoiceEloquentRepository;
use App\Infrastructure\Order\OrderEloquentRepository;
use Detection\MobileDetect;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\LazyLoadingViolationException;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\LivewireManager;
use Psr\Log\LoggerInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InvoiceRepository::class, InvoiceEloquentRepository::class);
        $this->app->bind(OrderRepository::class, OrderEloquentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());

        $this->app->make(LivewireManager::class)->addPersistentMiddleware([AuthMiddleware::class]);

        Model::preventLazyLoading();

        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation) {
            if (app()->isProduction()) {
                app(LoggerInterface::class)->warning("Lazy loading {$relation} on " . get_class($model));
                return;
            }

            throw new LazyLoadingViolationException($model, $relation);
        });

        require_once app_path('Helpers/helpers.php');

        $browser = new MobileDetect;

        View::share('browser', $browser);

        Blade::directive('dayDate', function ($expression) {
            return "<?php echo dayDate($expression); ?>";
        });

        Blade::directive("moneyFormat", function ($expression) {
            return "<?php echo moneyFormat($expression); ?>";
        });

        Blade::directive('unitPriceFormat', function ($expression) {
            return "<?php echo unitPriceFormat($expression); ?>";
        });;

        Blade::directive('numberFormat', function ($expression) {
            return "<?php echo numberFormat($expression); ?>";
        });

        Blade::directive('amountFormat', function ($expression) {
            return "<?php echo amountFormat($expression); ?>";
        });
    }
}
