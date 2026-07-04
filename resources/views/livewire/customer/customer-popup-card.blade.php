<x-ui.detail-popup-card>
    <div class="flex justify-center items-center">
        <span class="text-accent font-bold text-lg">{{$customer->customer_name}}</span>
    </div>
    <div class="flex flex-col gap-4">
        @foreach($customer->customPrices as $customPrice)
            @if($loop->first)
                <flux:separator/>
            @endif
            <div class="text-base flex gap-4 justify-between items-center">
                <span>{{$customPrice->product->product_name}} {{$customPrice->product->product_weight_g}}g</span>
                <span>@unitPriceFormat($customPrice->customer_product_price)</span>
            </div>
            <flux:separator/>
        @endforeach
    </div>
    <div class="flex gap-6 justify-center">
        <flux:link href="{{route('customers.edit', compact('customer'))}}">
            <flux:icon.pencil-square class="size-7"/>
        </flux:link>
    </div>
</x-ui.detail-popup-card>
