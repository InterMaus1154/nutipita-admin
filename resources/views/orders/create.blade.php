<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Place New Order"/>
        <div class="flex items-start">
            <flux:badge icon="exclamation-circle">0 amount products will be ignored</flux:badge>
        </div>
        <x-error/>
        <x-success />
        <h3 class="font-bold">Order Details</h3>
        <form action="{{route('orders.store')}}" method="POST" class="flex flex-col gap-4">
            @csrf
            <x-form.form-wrapper>
                <x-form.form-label id="order_placed_at" text="Order Placed At (default: today)"/>
                <x-form.form-input type="date" id="order_placed_at" name="order_placed_at" value="{{old('order_placed_at', request('order_placed_at', now()->toDateString()))}}"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper>
                <x-form.form-label id="order_due_at" text="Order Due At (default: for tomorrow, made today)"/>
                <x-form.form-input type="date" id="order_due_at" name="order_due_at" value="{{old('order_due_at', request('order_due_at', now()->addDay()->toDateString()))}}"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper>
                <x-form.form-label id="is_daytime">
                    <flux:badge color="cyan">Is Daytime?</flux:badge>
                </x-form.form-label>
                <input type="checkbox"
                       id="is_daytime"
                       name="is_daytime"
                       value="1"
                       @checked(old('is_daytime', request('is_daytime', 0)))
                       class="py-2.5 sm:py-3 px-4 block border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                >
            </x-form.form-wrapper>
            {{--livewire component for selecting customer with custom product prices--}}
            <livewire:product-selector />
            <flux:button variant="primary" type="submit">Add</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>

