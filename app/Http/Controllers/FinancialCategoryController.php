<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialCategoryRequest;
use App\Http\Requests\StoreFinancialCategoryRequest;
use App\Models\FinancialCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
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

    public function store(FinancialCategoryRequest $request): RedirectResponse
    {
        try{
            FinancialCategory::create([
                'fin_cat_name' => $request->input('fin_cat_name'),
            ]);
            return redirect()->route('financial-categories.index')->with('success', 'New category added!');
        }catch (\Throwable $e){
            Log::error('Error creating category. Error:');
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error at creating category. Contact your administrator.');
        }
    }

    public function edit(FinancialCategory $category): View
    {
        return view('fin-categories.edit', compact('category'));
    }

    public function update(FinancialCategoryRequest $request, FinancialCategory $category): RedirectResponse
    {
        \DB::beginTransaction();
        try{
            $category->update([
                'fin_cat_name' => $request->input('fin_cat_name'),
            ]);
            \DB::commit();
            return redirect()->route('financial-categories.index')->with('success', 'Category updated!');
        }catch (\Throwable $e){
            \DB::rollBack();
            Log::error('Error updating category. Error:');
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error at updating category. Contact your administrator.');
        }
    }
}

