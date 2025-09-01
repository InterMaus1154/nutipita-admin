<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialRecordController extends Controller
{
    // dashboard
    public function index(): View
    {
        return view('fin-records.index');
    }
}
