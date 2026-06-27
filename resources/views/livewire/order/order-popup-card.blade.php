<div>
    @if(!is_null($orderId))
        <x-ui.detail-popup-card>
            <div class="flex justify-center">
                <span class="text-lg text-accent font-bold">{{$order->customer->customer_name}}</span>
            </div>
            <div class="flex justify-between gap-4">
                <div class="flex gap-2">
                    @if($order->is_daytime)
                        <flux:badge color="yellow" variant="solid" size="sm">
                            <flux:icon.sun class="size-4 text-black"/>
                        </flux:badge>
                    @endif
                    @if(!$order->is_daytime)
                        <flux:badge color="violet" variant="solid" size="sm">
                            <flux:icon.moon class="size-4"/>
                        </flux:badge>
                    @endif
                    @if($order->is_standing)
                        <flux:badge color="teal" variant="solid" size="sm">
                            <flux:icon.arrow-path-rounded-square class="size-4 text-white"/>
                        </flux:badge>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 justify-between">
                <div class="flex gap-2 flex-col justify-center text-center">
                    <span>Placed:</span>
                    <span class="text-base">@dayDate($order->order_placed_at)</span>
                </div>
                <div class="flex gap-2 flex-col justify-center text-center">
                    <span>Due:</span>
                    <span class="text-base">@dayDate($order->order_due_at)</span>
                </div>
            </div>
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
            <div class="flex justify-between gap-4 items-center flex-wrap">
                <div class="flex gap-2 items-center">
                    <flux:icon.pita class="size-5 text-accent"/>
                    <span class="text-lg font-semibold">@amountFormat($order->total_pita)</span>
                </div>
                <div class="flex gap-2 items-center">
                    <flux:icon.circle-pound-sterling class="size-5 text-accent"/>
                    <span class="text-lg font-semibold">@moneyFormat($order->total_price)</span>
                </div>
            </div>
        </x-ui.detail-popup-card>
    @endif
</div>

