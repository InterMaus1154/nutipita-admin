<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// for checking if any standing order to be activated
Schedule::command('app:check-standing-order-status')
    ->dailyAt('02:00');

// creating order records from standing order
Schedule::command('app:create-order-from-standing')
    ->dailyAt('03:00');
