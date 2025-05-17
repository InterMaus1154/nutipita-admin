<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Controllers\CustomerController;

Route::group(['controller' => AuthController::class], function () {
    Route::get('/login', 'showLogin')->name('auth.view.login');
    Route::post('/login', 'login')->name('auth.login');
});

// routes with authentication
Route::group(['middleware' => AuthMiddleware::class], function () {

    // logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    // main dashboard
    Route::get('/', [AdminController::class, 'showDashboard'])->name('admin.view.dashboard');

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
    });

});
