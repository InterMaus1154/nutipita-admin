<?php

use App\Http\Controllers\FinancialCategoryController;
use App\Http\Controllers\FinancialRecordController;
use Illuminate\Support\Facades\Route;
// with financial categories

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
