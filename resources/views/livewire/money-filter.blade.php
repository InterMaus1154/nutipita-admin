<div class="space-y-4">
    <div class="flex flex-col gap-4 items-start">
        <div class="max-w-sm">
            <label for="customer_id" class="block text-sm font-medium mb-2 dark:text-white">Customer</label>
            <select id="customer_id" wire:model.live="customer_id"
                    class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                <option value="">---Select customer---</option>
                @foreach($customers as $customer)
                    <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-6">
            <div class="max-w-sm">
                <label for="due_from" class="block text-sm font-medium mb-2 dark:text-white">Due from:</label>
                <input type="date" id="due_from" wire:model.live="due_from"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="max-w-sm">
                <label for="due_to" class="block text-sm font-medium mb-2 dark:text-white">Due to:</label>
                <input type="date" id="due_to" wire:model.live="due_to"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
        </div>
        <div class="flex flex-wrap gap-4">
            <flux:button.group>
                <flux:button wire:click="setYear">Year</flux:button>
                <flux:button wire:click="setMonth">Month</flux:button>
                <flux:button wire:click="setWeek">Week</flux:button>
                <flux:button wire:click="setToday">Today</flux:button>
            </flux:button.group>
        </div>
    </div>
    <div class="flex gap-4 flex-wrap">
        <div
            class="flex flex-col gap-2 bg-white border border-gray-200 shadow-2xs rounded-xl p-4 md:p-5 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 text-center items-center text-xl">
            <span>Total Orders</span>
            <span>{{$orderCount}}</span>
        </div>
        <div
            class="flex flex-col gap-2 bg-white border border-gray-200 shadow-2xs rounded-xl p-4 md:p-5 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 text-center items-center text-xl">
            <span>Total Income</span>
            <span>£{{$totalIncome}}</span>
        </div>
    </div>
</div>
