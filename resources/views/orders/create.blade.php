<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Place New Order"/>
        <flux:badge icon="exclamation-circle">0 amount products will be ignored</flux:badge>
        <x-error/>
        <x-success />
        <h3 class="font-bold">Order Details</h3>
        <form action="{{route('orders.store')}}" method="POST" class="flex flex-col gap-4">
            @csrf
            <div class="max-w-sm">
                <label for="order_placed_at" class="block text-sm font-medium mb-2 dark:text-white">Order Placed At
                    (default: today)</label>
                <input type="date" id="order_placed_at" name="order_placed_at"
                       value="{{old('order_placed_at', now()->toDateString())}}"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="max-w-sm">
                <label for="order_due_at" class="block text-sm font-medium mb-2 dark:text-white">Order Due At</label>
                <input type="date" id="order_due_at" name="order_due_at" value="{{old('order_due_at', '')}}"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            {{--livewire component for selecting customer with custom product prices--}}
            @livewire('product-selector')
            <flux:button variant="primary" type="submit">Add</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
