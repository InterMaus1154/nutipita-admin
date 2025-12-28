@use(App\Enums\FinancialRecordType)
<div x-data="{selectedType: @entangle('selectedType')}">
    {{--show both--}}
    <div
        class="flex flex-col sm:grid gap-4"
        :class="selectedType === null ? 'grid-cols-2' : 'grid-cols-1'">
        <div
            x-show="selectedType === null || selectedType === @js(FinancialRecordType::INCOME->value)"
            x-cloak
            class="flex flex-col gap-4">
            <h2 class="text-center">Income</h2>
            <livewire:fin-record.financial-record-list :type="FinancialRecordType::INCOME"/>
        </div>
        <div
            x-show="selectedType === null || selectedType === @js(FinancialRecordType::EXPENSE->value)"
            x-cloak
            class="flex flex-col gap-4">
            <h2 class="text-center">Expense</h2>
            <livewire:fin-record.financial-record-list :type="FinancialRecordType::EXPENSE"/>
        </div>
    </div>
{{--    --}}{{--expense only--}}
{{--    <div--}}
{{--        x-show="selectedType === '@js(FinancialRecordType::EXPENSE->value)'"--}}
{{--        x-cloak--}}
{{--        class="flex flex-col gap-4">--}}
{{--        <h2 class="text-center">Expense</h2>--}}
{{--        <livewire:fin-record.financial-record-list :type="FinancialRecordType::EXPENSE"/>--}}
{{--    </div>--}}
{{--    --}}{{--income only--}}
{{--    <div--}}
{{--        x-show="selectedType === '@js(FinancialRecordType::INCOME->value)'"--}}
{{--        x-cloak--}}
{{--        class="flex flex-col gap-4">--}}
{{--        <h2 class="text-center">Income</h2>--}}
{{--        <livewire:fin-record.financial-record-list :type="FinancialRecordType::INCOME"/>--}}
{{--    </div>--}}
</div>
