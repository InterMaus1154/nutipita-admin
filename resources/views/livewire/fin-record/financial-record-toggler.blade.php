@use(App\Enums\FinancialRecordType)
<div>
    {{--show both--}}
    @if(is_null($selectedType))
        <div class="flex gap-4">
            <h2>Both selected</h2>
            <livewire:fin-record.financial-record-list />
            <div></div>
            <livewire:fin-record.financial-record-list />
        </div>
    @elseif($selectedType === FinancialRecordType::EXPENSE)
        <h2>Expense Selected</h2>
        <livewire:fin-record.financial-record-list />
    @elseif($selectedType === FinancialRecordType::INCOME)
        <h2>Income selected</h2>
        <livewire:fin-record.financial-record-list />
    @endif
</div>
