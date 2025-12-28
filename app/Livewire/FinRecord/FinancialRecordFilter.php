<?php

namespace App\Livewire\FinRecord;

use App\Enums\FinancialRecordType;
use App\Models\FinancialCategory;
use App\Traits\HasQuickDueFilter;
use Illuminate\View\View;
use Livewire\Component;

class FinancialRecordFilter extends Component
{
    use HasQuickDueFilter;

    public $financialCategories;
    public $category_id;

    public ?FinancialRecordType $selectedType = null;


    public function mount(): void
    {
        $this->setAfterChangeMethod('dispatchEvent');
//        $this->setCurrentMonth();
        $this->selectedType = null;
        $this->financialCategories = FinancialCategory::all();
        $this->year = now()->year;
    }

    public function toggleType(FinancialRecordType $type): void
    {
        if($this->selectedType === $type){
            $this->selectedType = null;
        }else{
            $this->selectedType = $type;
        }
        $this->dispatchSelectedType();
    }

    public function dispatchSelectedType(): void
    {
        $this->dispatch('update-selected-type', [
            'selectedType' => $this->selectedType
        ])->to(FinancialRecordToggler::class);
    }

    public function updated(): void
    {
        $this->dispatchEvent();
    }

    public function dispatchEvent(): void
    {
        // dispatch filter to list component
        $this->dispatch('update-filter', [
            'category_id' => $this->category_id,
            'due_from' => $this->due_from,
            'due_to' => $this->due_to
        ])->to(FinancialRecordList::class);
    }


    public function render(): View
    {
        return view('livewire.fin-record.financial-record-filter');
    }
}
