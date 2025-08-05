@use(App\Enums\OrderStatus)
<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Edit Order #{{$order->order_id}}"/>
        <div class="flex gap-2">
            <flux:badge>Customer:</flux:badge>
            <flux:link
                href="{{route('customers.show', ['customer' => $order->customer])}}">{{$order->customer->customer_name}}</flux:link>
        </div>
        <x-error/>
        <x-success/>
        <form action="{{route('orders.update', compact('order'))}}" method="POST" class="flex flex-col gap-4">
            @csrf
            @method('PUT')
            <x-form.form-wrapper>
                <x-form.form-label id="order_placed_at" text="Order Placed At"/>
                <x-form.form-input type="date" id="order_placed_at" name="order_placed_at"
                                   value="{{$order->order_placed_at}}"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper>
                <x-form.form-label id="order_due_at" text="Order Due At"/>
                <x-form.form-input type="date" id="order_due_at" name="order_due_at" value="{{$order->order_due_at}}"/>
            </x-form.form-wrapper>
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
            <h3>Products</h3>
            @foreach($products as $product)
                @php
                    $orderProduct = $order->products->firstWhere('product_id', $product->product_id);
                @endphp
                @if($product->price > 0)
                    <x-form.form-wrapper>
                        <x-form.form-label id="products[{{$product->product_id}}]">
                            {{$product->product_name}} {{$product->product_weight_g}}g -
                            <flux:badge>
                                @unitPriceFormat($product->price)
                            </flux:badge>
                        </x-form.form-label>
                        <x-form.form-input type="number" id="products[{{$product->product_id}}]" name="products[{{$product->product_id}}]" value="{{$orderProduct ? $orderProduct->pivot->product_qty : 0}}"/>
                    </x-form.form-wrapper>
                @endif

            @endforeach
            <flux:button variant="primary" type="submit">Update</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
