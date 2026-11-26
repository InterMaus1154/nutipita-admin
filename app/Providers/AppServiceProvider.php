<?php

namespace App\Providers;

use App\Domain\Invoice\Contracts\InvoiceRepository;
use App\Domain\Invoice\Events\InvoiceCreated;
use App\Domain\Order\Contracts\OrderRepository;
use App\Domain\Pdf\Events\FileFailed;
use App\Domain\Pdf\Events\FileSaved;
use App\Domain\Pdf\Listeners\FileFailedListener;
use App\Domain\Pdf\Listeners\FileSavedListener;
use App\Domain\Pdf\Listeners\InvoiceCreatedGeneratePdf;
use App\Domain\Product\Contracts\ProductRepository;
use App\Helpers\Dispatcher;
use App\Helpers\DispatcherInterface;
use App\Http\Middleware\AuthMiddleware;
use App\Infrastructure\Invoice\InvoiceEloquentRepository;
use App\Infrastructure\Order\OrderEloquentRepository;
use App\Infrastructure\Product\ProductEloquentRepository;
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
        $this->app->singleton(DispatcherInterface::class, Dispatcher::class);
        $this->app->bind(ProductRepository::class, ProductEloquentRepository::class);
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

        $dispatcher = $this->app->make(DispatcherInterface::class);

        $dispatcher->listen(InvoiceCreated::class, InvoiceCreatedGeneratePdf::class);
        $dispatcher->listen(FileSaved::class, FileSavedListener::class);
        $dispatcher->listen(FileFailed::class, FileFailedListener::class);
    }
}
