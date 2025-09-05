@use(App\Enums\FinancialRecordType)
<div>
    {{--show both--}}
    @if(is_null($selectedType))
        <div class="flex flex-col sm:grid grid-cols-2 gap-4">
            <div class="flex flex-col gap-4">
                <h2 class="text-center">Income</h2>
                <livewire:fin-record.financial-record-list :type="\App\Enums\FinancialRecordType::INCOME"/>
            </div>
            <div class="flex flex-col gap-4">
                <h2 class="text-center">Expense</h2>
                <livewire:fin-record.financial-record-list :type="\App\Enums\FinancialRecordType::EXPENSE"/>
            </div>
        </div>
    @elseif($selectedType === FinancialRecordType::EXPENSE)
        <div class="flex flex-col gap-4">
            <h2 class="text-center">Expense</h2>
            <livewire:fin-record.financial-record-list :type="\App\Enums\FinancialRecordType::EXPENSE"/>
        </div>
    @elseif($selectedType === FinancialRecordType::INCOME)
        <div class="flex flex-col gap-4">
            <h2 class="text-center">Income</h2>
            <livewire:fin-record.financial-record-list :type="\App\Enums\FinancialRecordType::INCOME"/>
        </div>
    @endif
</div>
