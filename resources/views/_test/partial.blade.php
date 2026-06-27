<div class="flex flex-col gap-4 my-4">
    @foreach($order->products as $orderProduct)
        @if($loop->first)
            <flux:separator/>
        @endif
        <div class="text-base flex gap-4 justify-between items-center">
            <span>{{$orderProduct->product_name}} {{$orderProduct->product_weight_g}}g</span>
            <div class="flex flex-col gap-1 justify-center items-center text-center">
                <span>@amountFormat($orderProduct->pivot->product_qty) x @unitPriceFormat($orderProduct->pivot->order_product_unit_price)</span>
                <span>@moneyFormat($orderProduct->pivot->product_qty * $orderProduct->pivot->order_product_unit_price)</span>
            </div>
        </div>
        <flux:separator/>
    @endforeach
</div>
