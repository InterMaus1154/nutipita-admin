<?php

namespace App\Livewire\FinRecord;

use App\Enums\FinancialRecordType;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class FinancialRecordList extends Component
{

    public ?FinancialRecordType $selectedType = null;
    public array $filters;

    #[On('update-filter')]
    public function applyFilter(array $filters): void
    {
        $this->filters = $filters;
    }

    public function mount(?FinancialRecordType $type): void
    {
        $this->selectedType = $type;
    }

    public function render(): View
    {
        return view('livewire.fin-record.financial-record-list');
    }
}
