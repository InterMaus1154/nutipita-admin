<div class="space-y-4">
    <div class="flex flex-col gap-4 items-start flex-wrap sm:grid grid-cols-3 sm:items-center">
        <x-form.form-wrapper center="true">
            <label for="customer_id" class="block text-sm font-medium mb-2 dark:text-white">Customer</label>
            <select id="customer_id" wire:model.live="customer_id"
                    class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                <option value=""></option>
                @foreach($customers as $customer)
                    <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                @endforeach
            </select>
        </x-form.form-wrapper>
        <div class="flex flex-col items-center flex-wrap gap-2">
            <x-form.form-label text="Due date range"/>
            <div class="flex flex-wrap gap-4">
                <x-form.quick-date-buttons :activePeriod="$activePeriod" :months="$months"/>
            </div>
        </div>
        <div class="flex gap-6 justify-self-end">
            <x-form.form-wrapper center="true">
                <label for="due_from" class="block text-sm font-medium mb-2 dark:text-white">Due from:</label>
                <input type="date" id="due_from" wire:model.live="due_from"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </x-form.form-wrapper>
            <x-form.form-wrapper center="true">
                <label for="due_to" class="block text-sm font-medium mb-2 dark:text-white">Due to:</label>
                <input type="date" id="due_to" wire:model.live="due_to"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </x-form.form-wrapper>
        </div>
    </div>
    <div class="flex gap-4 flex-wrap">
        <x-data-box dataBoxHeader="Total Orders" :dataBoxValue="numberFormat($orderCount, 0)"/>
        <x-data-box dataBoxHeader="Total Income" :dataBoxValue=" moneyFormat($totalIncome)"/>
    </div>
</div>
