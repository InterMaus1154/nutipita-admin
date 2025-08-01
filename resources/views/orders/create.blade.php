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
                <x-form.form-label id="order_placed_at" text="Order Placed At"/>
                <x-form.form-input type="date" id="order_placed_at" name="order_placed_at" value="{{old('order_placed_at', request('order_placed_at', now()->toDateString()))}}"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper>
                <x-form.form-label id="order_due_at" text="Order Due At"/>
                <x-form.form-input type="date" id="order_due_at" name="order_due_at" value="{{old('order_due_at', request('order_due_at', now()->toDateString()))}}"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper>
                <x-form.form-label id="is_daytime">
                    <flux:badge color="cyan">Is Daytime?</flux:badge>
                </x-form.form-label>
                <x-form.form-input type="checkbox" id="is_daytime" name="is_daytime" noFullWidth="true" value="1"/>
            </x-form.form-wrapper>
            {{--livewire component for selecting customer with custom product prices--}}
            <livewire:product-selector />
            <flux:button variant="primary" type="submit">Add</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>

