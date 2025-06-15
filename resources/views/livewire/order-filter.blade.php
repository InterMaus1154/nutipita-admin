<div class="flex flex-col gap-4 items-start">
    <div class="max-w-sm">
        <label for="customer_id" class="block text-sm font-medium mb-2 dark:text-white">Customer</label>
        <select id="customer_id" wire:model.live="customer_id" class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
            <option value="">---Select customer---</option>
            @foreach($customers as $customer)
                <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-6 space-x-1.5">
        <div class="max-w-sm">
            <label for="due_from" class="block text-sm font-medium mb-2 dark:text-white">Due From</label>
            <input type="date" id="due_from" wire:model.live="due_from" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="Due From">
        </div>
        <div class="max-w-sm">
            <label for="due_to" class="block text-sm font-medium mb-2 dark:text-white">Due From</label>
            <input type="date" id="due_to" wire:model.live="due_to" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" placeholder="Due To">
        </div>
    </div>
    <div class="max-w-sm">
        <label for="order_status" class="block text-sm font-medium mb-2 dark:text-white">Status</label>
        <select id="order_status" wire:model.live="status" class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
            <option value="">---Select status---</option>
            @foreach(\App\Enums\OrderStatus::cases() as $orderStatus)
                <option value="{{$orderStatus->name}}">{{ucfirst($orderStatus->value)}}</option>
            @endforeach
        </select>
    </div>
    <flux:button class="cursor-pointer" wire:click="clearFilter">Clear filter</flux:button>
</div>
