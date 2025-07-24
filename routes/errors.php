<?php

use Illuminate\Support\Facades\Route;

/*
 * Custom error routes are defined here
 */
Route::prefix('error')->group(function () {

    // MethodNotAllowedHttpException
    Route::get('405', function () {
        return response()->view('errors.405', [], 405);
    })->name('errors.405');

    // NotFoundHttpException
    Route::get('404', function(){
        return response()->view('errors.404', [], 404);
    })->name('errors.404');
});
