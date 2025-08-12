<div class="space-y-4">
    <div class="flex flex-col gap-4 items-start flex-wrap sm:grid grid-cols-3 sm:items-center">
        {{--customer input--}}
        <x-form.customer-select />
        <div class="flex flex-col items-center flex-wrap gap-2">
            <x-form.form-label text="Due date range"/>
            <div class="flex flex-wrap gap-4">
                <x-form.quick-date-buttons :activePeriod="$activePeriod" :months="$months"/>
            </div>
        </div>
        <div class="flex gap-6 justify-self-end">
            <x-form.form-wrapper>
                <x-form.form-label id="due_from" text="Due From"/>
                <x-form.form-input type="date" id="due_from" wireModelLive="due_from"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper>
                <x-form.form-label id="due_to" text="Due To"/>
                <x-form.form-input type="date" id="due_to" wireModelLive="due_to"/>
            </x-form.form-wrapper>
        </div>
    </div>
    <div class="flex gap-4 flex-wrap">
        <x-data-box dataBoxHeader="Total Orders" :dataBoxValue="numberFormat($orderCount, 0)"/>
        <x-data-box dataBoxHeader="Total Income" :dataBoxValue=" moneyFormat($totalIncome)"/>
    </div>
</div>
