@use(App\Enums\OrderStatus)
<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Edit Order #{{$order->order_id}}"/>
        <div class="flex gap-2 justify-center">
            <flux:badge class="px-4 py-2 !text-lg text-accent!">
                {{$order->customer->customer_name}}
            </flux:badge>
        </div>
        <x-error/>
        <x-success/>
        <form action="{{route('orders.update', compact('order'))}}" method="POST" class="flex flex-col gap-4 mt-6">
            @csrf
            @method('PUT')
            <div class="flex flex-col sm:grid grid-cols-3 items-start gap-4">
                {{--products--}}
                <div class="flex flex-col gap-4">
                    @foreach($products as $product)
                        @php
                            $orderProduct = $order->products->firstWhere('product_id', $product->product_id);
                        @endphp
                        @if($product->price > 0)
                            <x-form.form-wrapper>
                                <x-form.form-label id="products[{{$product->product_id}}]">
                                    {{$product->product_name}} {{$product->product_weight_g}}g
                                    - @unitPriceFormat($product->price)
                                </x-form.form-label>
                                <x-form.form-input type="number" id="products[{{$product->product_id}}]"
                                                   name="products[{{$product->product_id}}]"
                                                   value="{{$orderProduct ? $orderProduct->pivot->product_qty : ''}}"
                                                   placeholder="0"
                                />
                            </x-form.form-wrapper>
                        @endif
                    @endforeach
                </div>
                {{--statuses--}}
                <div class="flex gap-4 justify-self-center sm:flex-row flex-col">
                    {{--shift type--}}
                    <x-form.form-wrapper center="true">
                        <x-form.form-label id="shift" text="Shift"/>
                        <x-form.form-select id="shift" name="shift">
                            <option value="night" {{!$order->is_daytime ? 'selected' : ''}}>Night</option>
                            <option value="day" {{$order->is_daytime ? 'selected': ''}}>Day</option>
                        </x-form.form-select>
                    </x-form.form-wrapper>
                    {{--order status--}}
                    <x-form.form-wrapper>
                        <x-form.form-label id="order_status" text="Order Status"/>
                        <x-form.form-select name="order_status" id="order_status">
                            @foreach(OrderStatus::cases() as $status)
                                <option
                                    value="{{$status->name}}"
                                    {{$status->name === $order->order_status ? "selected" : ""}}>
                                    {{ucfirst($status->value)}}
                                </option>
                            @endforeach
                        </x-form.form-select>
                    </x-form.form-wrapper>
                </div>
                {{--order dates--}}
                <div class="flex gap-4 justify-self-end flex-row sm:flex-row">
                    {{--order placed--}}
                    <x-form.form-wrapper>
                        <x-form.form-label id="order_placed_at" text="Placed At"/>
                        <x-form.form-input type="date" id="order_placed_at" name="order_placed_at"
                                           value="{{$order->order_placed_at}}"/>
                    </x-form.form-wrapper>
                    {{--order due--}}
                    <x-form.form-wrapper>
                        <x-form.form-label id="order_due_at" text="Due At"/>
                        <x-form.form-input type="date" id="order_due_at" name="order_due_at" value="{{$order->order_due_at}}"/>
                    </x-form.form-wrapper>
                </div>
            </div>


            <flux:button variant="primary" type="submit">Update</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
