<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Place New Order"/>
        <x-error/>
        <x-success />
        <form action="{{route('orders.store')}}" method="POST" class="flex flex-col gap-4">
            @csrf
            <div class="flex flex-wrap gap-4 sm:grid grid-cols-3">
                <livewire:product-selector />
                <div class="justify-self-center">
                    <x-form.form-wrapper>
                        <x-form.form-label id="shift" text="Shift"/>
                        <x-form.form-select id="shift" name="shift">
                            <option value="night" {{request('is_daytime') ? '' : 'selected'}}>Night</option>
                            <option value="day" {{request('is_daytime') ? 'selected' : ''}}>Day</option>
                        </x-form.form-select>
                    </x-form.form-wrapper>
                </div>
                <div class="flex gap-4 justify-self-end">
                    <x-form.form-wrapper>
                        <x-form.form-label id="order_placed_at" text="Placed At"/>
                        <x-form.form-input type="date" id="order_placed_at" name="order_placed_at" value="{{old('order_placed_at', request('order_placed_at', now()->toDateString()))}}"/>
                    </x-form.form-wrapper>
                    <x-form.form-wrapper>
                        <x-form.form-label id="order_due_at" text="Due At"/>
                        <x-form.form-input type="date" id="order_due_at" name="order_due_at" value="{{old('order_due_at', request('order_due_at', now()->addDay()->toDateString()))}}"/>
                    </x-form.form-wrapper>
                </div>
            </div>
            <flux:button variant="primary" type="submit">Add</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>

