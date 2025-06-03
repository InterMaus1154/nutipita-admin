<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('app:check-standing-order-status')
    ->dailyAt('02:00');
Schedule::command('app:create-order-from-standing')
    ->dailyAt('03:00');
