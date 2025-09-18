<?php
use Illuminate\Support\Facades\Route;
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
