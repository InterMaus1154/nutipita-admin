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
use App\Http\Controllers\FinancialRecordController;
use App\Http\Controllers\FinancialCategoryController;
use App\Http\Controllers\CreditNoteController;

Route::group(['controller' => AuthController::class], function () {
    Route::get('/login', 'showLogin')->name('auth.view.login');
    Route::post('/login', 'login')->name('auth.login');
});

// routes with authentication
Route::group(['middleware' => AuthMiddleware::class], function () {

    // logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

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


    // standing order routes
    Route::group(['prefix' => 'standing-orders', 'controller' => StandingOrderController::class], function () {

        // index page
        Route::get('/', 'index')->name('standing-orders.index');

        // create form
        Route::get('/create', 'create')->name('standing-orders.create');

        // store new standing order
        Route::post('/', 'store')->name('standing-orders.store');

        // show details page
        Route::get('/show/{order}', 'show')->name('standing-orders.show');

        // show edit page
        Route::get('/edit/{order}', 'edit')->name('standing-orders.edit');

        // update standing order
        Route::put('/update/{order}', 'update')->name('standing-orders.update');
    });

    // financial records & categories
    Route::group(['prefix' => 'financial-records'], function () {

        // records only
        Route::group(['controller' => FinancialRecordController::class], function () {
            Route::get('/', 'index')->name('financial-records.index');
            Route::get('/create', 'create')->name('financial-records.create');
            Route::post('/', 'store')->name('financial-records.store');
            Route::get('/edit/{record}', 'edit')->name('financial-records.edit');
            Route::put('/update/{record}', 'update')->name('financial-records.update');
        });

        // cats only
        Route::group(['prefix' =>'categories' ,'controller' => FinancialCategoryController::class], function () {
           Route::get('/', 'index')->name('financial-categories.index');
           Route::get('/create', 'create')->name('financial-categories.create');
           Route::post('/', 'store')->name('financial-categories.store');
           Route::get('/edit/{category}', 'edit')->name('financial-categories.edit');
           Route::put('/update/{category}', 'update')->name('financial-categories.update');
        });
    });

    // credit notes
    Route::group(['prefix' => 'credit-notes', 'controller' => CreditNoteController::class], function(){
        Route::get('/test', 'test')->name('credit-notes.test');
    });

    Route::get('/test/select', function(){
        return view('_test.select');
    });

    require __DIR__ . '/errors.php';
});
