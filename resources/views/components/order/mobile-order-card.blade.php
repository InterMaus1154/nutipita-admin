@props(['order'])
<x-ui.mobile-card-skeleton {{$attributes}}>
    <div class="flex gap-4 justify-between">
        <div class="flex gap-2">
            <x-order.order-status-select :order_id="$order->order_id" :order_status="$order->order_status"/>
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
        <x-ui.mobile-card-dropdown-menu>
            <x-ui.mobile-card-dropdown-link href="{{route('orders.show', ['order' => $order->order_id])}}">View
            </x-ui.mobile-card-dropdown-link>
            <x-ui.mobile-card-dropdown-link title="Edit Order" x-data
                                            @click="$dispatch('modal-open', {component: 'modal.order-edit', componentData: { order_id: {{$order->order_id}} } })">
                Edit
            </x-ui.mobile-card-dropdown-link>
            @unless($order->invoice_id)
                <x-ui.mobile-card-dropdown-link wire:confirm="Are you sure to generate this invoice?"
                                                wire:click="createInvoice({{$order->order_id}})">Generate
                    Invoice
                </x-ui.mobile-card-dropdown-link>
            @else
                <x-ui.mobile-card-dropdown-link href="{{route('invoices.download', ['invoice' => $order->invoice_id])}}">
                    Download
                    Invoice
                </x-ui.mobile-card-dropdown-link>
            @endunless
            <x-ui.mobile-card-dropdown-link wire:click="deleteOrder({{$order->order_id}})"
                                            wire:confirm="Are you sure to delete order # {{$order->order_id}} ? This action cannot be undone!">
                Delete
            </x-ui.mobile-card-dropdown-link>
        </x-ui.mobile-card-dropdown-menu>
    </div>
    <div class="flex justify-between gap-4 items-center">
                        <span class="text-accent">
                            {{$order->customer_name}}
                        </span>
        <div class="flex gap-2">
                        <span class="text-base">
                            @dayDate($order->order_due_at)
                        </span>
        </div>
    </div>
    <div class="flex justify-between gap-4 items-center flex-wrap">
        <div class="flex gap-2 items-center">
            <flux:icon.pita class="size-5 text-accent"/>
            <span class="text-base">@amountFormat($order->total_pita)</span>
        </div>
        <div class="flex gap-2 items-center">
            <flux:icon.circle-pound-sterling class="size-5 text-accent"/>
            <span class="text-base">@moneyFormat($order->total_price)</span>
        </div>
    </div>
    <flux:button @click="$dispatch('modal-open', { component: 'order.order-popup-card', componentData: { orderId: {{$order->order_id}} } })">
        <flux:icon.chevron-double-up class="text-accent"/>
    </flux:button>
</x-ui.mobile-card-skeleton>
