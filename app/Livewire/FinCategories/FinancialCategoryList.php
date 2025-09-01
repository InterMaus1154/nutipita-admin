<?php

namespace App\Livewire\FinCategories;

use App\Models\FinancialCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;

class FinancialCategoryList extends Component
{

    public function delete(FinancialCategory $category): void
    {
        if(!auth()->check()){
            abort(403);
        }

        DB::beginTransaction();
        try{
            $category->delete();
            session()->flash('success', 'Category successfully deleted');
            DB::commit();
        }catch (\Throwable $e){
            DB::rollBack();
            Log::error('Error deleting financial category. Error:');
            Log::error($e->getMessage());
        }
    }

    public function render(): View
    {
        $financialCategories = FinancialCategory::all();
        return view('livewire.fin-categories.financial-category-list', compact('financialCategories'));
    }
}
