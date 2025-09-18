<?php
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthMiddleware;

Route::get('/login', 'showLogin')->name('auth.view.login');
Route::post('/login', 'login')->name('auth.login');

Route::post('/logout', 'logout')->name('auth.logout')->middleware(AuthMiddleware::class);
