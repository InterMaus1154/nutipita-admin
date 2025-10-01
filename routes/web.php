<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\StandingOrderController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\UserSettingController;


// auth routes
Route::prefix('auth')->controller(AuthController::class)->group(base_path('routes/route-groups/auth.php'));

// routes with authentication
Route::group(['middleware' => AuthMiddleware::class], function () {

    // main dashboard
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('admin.view.dashboard');
    Route::get('/', fn() => redirect()->route('admin.view.dashboard'));

    Route::get('/dashboard/index', fn() => redirect()->route('admin.view.dashboard'))
        ->name('dashboard.index');

    // create today order total pdf
    Route::get('/today-order-pdf', [DashboardController::class, 'createOrderTotalPdf'])->name('today.order.pdf');


    Route::prefix('customers')->controller(CustomerController::class)->group(base_path('routes/route-groups/customers.php'));
    Route::prefix('products')->controller(ProductController::class)->group(base_path('routes/route-groups/products.php'));
    Route::prefix('orders')->controller(OrderController::class)->group(base_path('routes/route-groups/orders.php'));
    Route::prefix('invoices')->controller(InvoiceController::class)->group(base_path('routes/route-groups/invoices.php'));
    Route::prefix('standing-orders')->controller(StandingOrderController::class)->group(base_path('routes/route-groups/standing-orders.php'));
    Route::prefix('financial-records')->group(base_path('routes/route-groups/financial-records.php'));
    Route::prefix('settings')->controller(UserSettingController::class)->group(base_path('routes/route-groups/user-settings.php'));

    // credit notes
    Route::group(['prefix' => 'credit-notes', 'controller' => CreditNoteController::class], function(){
        Route::get('/test', 'test')->name('credit-notes.test');
    });

    Route::get('/test/select', function(){
        return view('_test.select');
    });

    require __DIR__ . '/errors.php';
});
