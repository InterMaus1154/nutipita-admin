<?php

use Illuminate\Support\Facades\Route;


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
