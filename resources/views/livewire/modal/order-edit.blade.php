<div>
    <x-modal.wrapper title="Edit Order" size="4xl">
        <x-slot:content>
            @error('general_error')
            <x-form.input-error :message="$message"/>
            @enderror
            <div class="py-8 flex justify-between flex-wrap gap-4 sm:grid grid-cols-[1fr_1fr_1fr]">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-col gap-4 items-start">
                        @foreach($customerProducts as $product)
                            @php
                                $qty = $order->products->find($product->product_id)?->pivot->product_qty ?? 0;
                            @endphp
                            <x-form.form-wrapper>
                                <x-form.form-label id="products-{{$product->product_id}}">
                                    {{$product->product_name}} {{$product->product_weight_g}}g
                                    - @unitPriceFormat($product->price)
                                </x-form.form-label>
                                <x-form.form-input type="number" id="products-{{$product->product_id}}"
                                                   placeholder="0"
                                                   wire-model="selectedProducts.{{$product->product_id}}"
                                />
                            </x-form.form-wrapper>
                        @endforeach
                    </div>
                    @error('products')
                    <x-form.input-error :message="$message"/>
                    @enderror
                </div>
                <x-form.form-wrapper>
                    <x-form.form-label id="shift" text="Shift"/>
                    <x-ui.select :has-wire="true" wire-model="fields.shift" wrapper-class="max-w-[150px]">
                        <x-slot:options>
                            <x-ui.select.option value="night" text="Night"></x-ui.select.option>
                            <x-ui.select.option value="day" text="Day"></x-ui.select.option>
                        </x-slot:options>
                    </x-ui.select>
                </x-form.form-wrapper>
                <div class="flex gap-2">
                    {{--placed date--}}
                    <x-form.form-wrapper>
                        <x-form.form-label text="Placed At" id="order_placed_at"/>
                        <x-form.form-input id="order_placed_at" type="date" name="order_placed_at"
                                           wire-model-live="fields.order_placed_at"/>
                        @error('order_placed_at')
                        <x-form.input-error :message="$message"/>
                        @enderror
                    </x-form.form-wrapper>
                    {{--due date--}}
                    <x-form.form-wrapper>
                        <x-form.form-label text="Due At" id="order_due_at"/>
                        <x-form.form-input id="order_due_at" type="date" name="order_due_at"
                                           wire-model-live="fields.order_due_at"/>

                        @error('order_due_at')
                        <x-form.input-error :message="$message"/>
                        @enderror
                    </x-form.form-wrapper>
                </div>


            </div>
        </x-slot:content>
        <x-slot:footer>
            <div class="flex justify-end gap-4">
                <flux:button variant="primary" wire:click="save">Edit</flux:button>
                <flux:button variant="danger" wire:click="cancel">Cancel</flux:button>
            </div>
        </x-slot:footer>
    </x-modal.wrapper>
</div>
