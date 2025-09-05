<?php

namespace App\Http\Controllers;

use App\Models\FinancialCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialRecordController extends Controller
{
    // dashboard
    public function index(): View
    {
        return view('fin-records.index');
    }

    // create form
    public function create(): View
    {
        $categories = FinancialCategory::all();
        return view('fin-records.create', compact('categories'));
    }
}
