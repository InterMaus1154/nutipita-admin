<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MoneyController extends Controller
{
    public function __invoke(): View
    {
        return view('money.index');
    }
}
