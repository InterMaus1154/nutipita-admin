<?php

namespace App\Livewire\FinRecord;

use App\Enums\FinancialRecordType;
use App\Models\FinancialRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class FinancialRecordList extends Component
{

    public ?FinancialRecordType $selectedType = null;
    public array $filters = [];

    #[On('update-filter')]
    public function applyFilter(array $filters): void
    {
        $this->filters = $filters;
    }

    public function mount(FinancialRecordType $type): void
    {
        $this->selectedType = $type;
    }

    public function buildQuery(): Builder
    {
        $filters = $this->filters;
        return FinancialRecord::query()
            ->where('financial_records.fin_record_type', $this->selectedType)
            ->when(!empty($filters['category_id']), function (Builder $query) use ($filters) {
                $query->where('financial_records.fin_cat_id', $filters['category_id']);
            })
            ->when(!empty($filters['due_from']), function (Builder $query) use ($filters) {
                return $query->whereDate('financial_records.fin_record_date', '>=', $filters['due_from']);
            })
            ->when(!empty($filters['due_to']), function (Builder $query) use ($filters) {
                return $query->whereDate('financial_records.fin_record_date', '<=', $filters['due_to']);
            });

    }

    public function render(): View
    {
        $records = $this->buildQuery()->get();
        return view('livewire.fin-record.financial-record-list', compact('records'));
    }
}
