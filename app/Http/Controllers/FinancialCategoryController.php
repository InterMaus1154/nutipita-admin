<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFinancialCategoryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FinancialCategoryController extends Controller
{
    // dashboard
    public function index(): View
    {
        return view('fin-categories.index');
    }

    public function create(): View
    {
        return view('fin-categories.create');
    }

    public function store(StoreFinancialCategoryRequest $request): RedirectResponse
    {

    }
}

