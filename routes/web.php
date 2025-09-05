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

    // routes with customers
    Route::group(['prefix' => 'customers', 'controller' => CustomerController::class], function () {

        // show customer list page
        Route::get('/', 'index')->name('customers.index');

        // show customer create form
        Route::get('/create', 'create')->name('customers.create');

        // store new customer
        Route::post('/', 'store')->name('customers.store');

        // show a customer
        Route::get('/show/{customer}', 'show')->name('customers.show');

        // show edit form
        Route::get('/edit/{customer}', 'edit')->name('customers.edit');

        // update a customer
        Route::put('/update/{customer}', 'update')->name('customers.update');

        // show edit/create custom price form
        Route::get('/edit/customPrice/{customer}', 'editCustomPrice')->name('customers.edit.custom-price');

        // update or add custom prices
        Route::put('/update/customPrice/{customer}', 'updateCustomPrice')->name('customers.update.custom-price');

        // remove a custom price
        Route::delete('/delete/customPrice/{customer}/{customPrice}', 'deleteCustomPrice')->name('customers.delete.custom-price');
    });

    // product routes
    Route::group(['prefix' => 'products', 'controller' => ProductController::class], function () {

        // product list page
        Route::get('/', 'index')->name('products.index');

        // show create form
        Route::get('/create', 'create')->name('products.create');

        // store new product
        Route::post('/', 'store')->name('products.store');

        // show a product detail
        Route::get('/show/{product}', 'show')->name('products.show');

        // show edit form
        Route::get('/edit/{product}', 'edit')->name('products.edit');

        // update product details
        Route::put('/update/{product}', 'update')->name('products.update');
    });

    // order routes
    Route::group(['prefix' => 'orders', 'controller' => OrderController::class], function () {

        // order list page
        Route::get('/', 'index')->name('orders.index');

        // show order creation form
        Route::get('/create', 'create')->name('orders.create');

        // store new order
        Route::post('/', 'store')->name('orders.store');

        // show an order
        Route::get('/show/{order}', 'show')->name('orders.show');

        // order edit form
        Route::get('/edit/{order}', 'edit')->name('orders.edit');

        // update order
        Route::put('/update/{order}', 'update')->name('orders.update');

        // create summary pdf
        Route::get('/create-summary-pdf', 'createSummaryPdf')->name('orders.create-summary-pdf');
    });

    // invoice routes
    Route::group(['prefix' => 'invoices', 'controller' => InvoiceController::class], function () {
//        Route::get('/createInvoice/{order}', 'createInvoice')->name('invoices.create');

        // invoice list
        Route::get('/', 'index')->name('invoices.index');

        // create form
//        Route::get('/createFromOrder', 'create')->name('invoices.create');

        // download an invoice
        Route::get('/download/{invoice}', 'download')->name('invoices.download');

        // view a pdf in browser
        Route::get('/view-inline/{invoice}', 'viewInline')->name('invoices.view-inline');

        // create manual invoice form
        Route::get('/create', 'createManual')->name('invoices.create');

        // create single
        Route::get('/create-single/{order}', 'createSingleInvoice')->name('invoices.create-single');

    });

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

    require __DIR__ . '/errors.php';
});
