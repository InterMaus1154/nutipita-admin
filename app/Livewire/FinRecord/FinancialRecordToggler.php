<?php

namespace App\Livewire\FinRecord;

use App\Enums\FinancialRecordType;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class FinancialRecordToggler extends Component
{

    public ?FinancialRecordType $selectedType = null;

    #[On('update-selected-type')]
    public function updateSelectedType(array $data): void
    {
        $this->selectedType = is_null($data['selectedType']) ? null : FinancialRecordType::from($data['selectedType']) ;
    }

    public function render(): View
    {
        return view('livewire.fin-record.financial-record-toggler');
    }
}
