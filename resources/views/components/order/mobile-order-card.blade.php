@props(['order'])
<x-ui.mobile-card-skeleton>
    {{--card header--}}
    <div class="flex gap-4 justify-between">
        {{--status badges--}}
        <div class="flex gap-2">
            {{--normal status badge--}}
            <x-order.order-status-select :order="$order"/>
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
        {{--dropdown menu for actions--}}
        <x-ui.mobile-card-dropdown-menu>
            <flux:link class="py-1 px-4 rounded-sm hover:bg-neutral-500/50 "
                       href="{{route('orders.show', compact('order'))}}">View
            </flux:link>
            <flux:link class="py-1 px-4 rounded-sm hover:bg-neutral-500/50 "
                       href="{{route('orders.edit', compact('order'))}}">Edit
            </flux:link>
            @unless($order->invoice)
                <flux:link class="py-1 px-4 rounded-sm hover:bg-neutral-500/50! block! "
                           href="{{route('invoices.create-single', compact('order'))}}">
                    Generate Invoice
                </flux:link>
            @else
                <flux:link class="py-1 px-4 rounded-sm hover:bg-neutral-500/50 "
                           href="{{route('invoices.download', ['invoice' => $order->invoice])}}">
                    Download
                    Invoice
                </flux:link>
            @endunless
            <flux:link class="py-1 px-4 rounded-sm hover:bg-neutral-500/50 cursor-pointer"
                       wire:click="deleteOrder({{$order->order_id}})"
                       wire:confirm="Are you sure to delete order # {{$order->order_id}} ? This action cannot be undone!">
                Delete
            </flux:link>
        </x-ui.mobile-card-dropdown-menu>
    </div>
    {{--customer and due date info--}}
    <div class="flex justify-between gap-4 items-center">
                        <span class="text-accent">
                            {{$order->customer->customer_name}}
                        </span>
        <div class="flex gap-2">
                        <span class="text-base font-semibold">
                            @dayDate($order->order_due_at)
                        </span>
        </div>
    </div>
    {{--total pita and price info--}}
    <div class="flex justify-between gap-4 items-center flex-wrap">
        <div class="flex gap-2">
            <flux:badge color="indigo">Pita:</flux:badge>
            <span class="text-base">@amountFormat($order->total_pita)</span>
        </div>
        <div class="flex gap-2">
            <flux:badge color="indigo">£:</flux:badge>
            <span class="text-base">@moneyFormat($order->total_price)</span>
        </div>
    </div>
    <x-ui.detail-popup-card>
        <div class="flex justify-center">
            <span class="text-lg! text-white! font-bold">{{$order->customer->customer_name}}</span>
        </div>
        <div class="flex justify-between gap-4">
            {{--status badge--}}
            <x-order.order-status-select/>
            {{--other badges--}}
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
        {{--product wrapper--}}
        <div class="flex flex-col gap-4 my-4">
            @foreach($order->products as $orderProduct)
                @if($loop->first)
                    <flux:separator/>
                @endif
                <div class="text-base flex gap-4 justify-between items-center">
                    <span>{{$orderProduct->product_name}}</span>
                    <div class="flex flex-col gap-1 justify-center items-center text-center">
                        <span>@amountFormat($orderProduct->pivot->product_qty) x @unitPriceFormat($orderProduct->pivot->order_product_unit_price)</span>
                        <span>@moneyFormat($orderProduct->pivot->product_qty * $orderProduct->pivot->order_product_unit_price)</span>
                    </div>
                </div>
                <flux:separator/>
            @endforeach
        </div>
        {{--        total pita and price info--}}
        <div class="flex justify-between gap-4 items-center flex-wrap">
            <div class="flex gap-2">
                <span class="text-lg text-accent font-semibold">@amountFormat($order->total_pita)</span>
            </div>
            <div class="flex gap-2">
                <span class="text-lg text-accent font-semibold">@moneyFormat($order->total_price)</span>
            </div>
        </div>
        {{--action buttons--}}
        <div class="flex gap-6 justify-center">
            <flux:link href="{{route('orders.edit', compact('order'))}}" title="Edit order">
                <flux:icon.pencil-square class="size-7"/>
            </flux:link>
            @unless($order->invoice)
                <flux:link href="{{route('invoices.create-single', compact('order'))}}"
                           title="Create invoice">
                    <flux:icon.clipboard-document-list class="size-7"/>
                </flux:link>
            @else
                <flux:link href="{{route('invoices.download', ['invoice' => $order->invoice])}}"
                           title="Download invoice">
                    <flux:icon.clipboard-document-check class="size-7"/>
                </flux:link>
            @endunless
            <flux:link class="cursor-pointer" wire:click="deleteOrder({{$order->order_id}})"
                       wire:confirm="Are you sure to delete order #{{$order->order_id}} for {{$order->customer->customer_name}}? This action cannot be undone!">
                <flux:icon.trash class="size-7"/>
            </flux:link>
        </div>
    </x-ui.detail-popup-card>
</x-ui.mobile-card-skeleton>
