<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinancialRecordRequest;
use App\Models\FinancialCategory;
use App\Models\FinancialRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

    public function store(FinancialRecordRequest $request): RedirectResponse
    {
        DB::beginTransaction();
        try{
            FinancialRecord::create($request->validated());
            DB::commit();
            return redirect()->route('financial-records.index')->with('success', 'Record successfully added');
        }catch (\Throwable $e){
            DB::rollBack();
            Log::error("Error adding financial record: ");
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'Error creating record. If issue persists, contact your administrator!');
        }
    }
}
