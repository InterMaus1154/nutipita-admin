<?php
use Illuminate\Support\Facades\Route;

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
