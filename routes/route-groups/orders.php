<?php
use Illuminate\Support\Facades\Route;

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
