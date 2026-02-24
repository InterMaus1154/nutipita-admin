@props(['order'])
<x-ui.mobile-card-skeleton>
    {{--card header--}}
    <div class="grid grid-cols-[1fr_1fr]">
        {{--badge--}}
        <div class="justify-self-start">
            @php
                if($order->is_active){
                    $bgColor = "bg-green-500!";
                    $shadowColor = "oklch(72.3% 0.219 149.579)";
                }else{
                    $bgColor = "bg-red-500!";
                    $shadowColor = "oklch(63.7% 0.237 25.331)";
                }
            @endphp
            <x-form.form-wrapper>
                <x-ui.select.select wire-change="updateOrderStatus"
                                    :wire-change-prop="$order->standing_order_id"
                                    :pre-selected-value="$order->is_active"
                                    inner-class="text-black text-sm! outline-0!"
                                    :bg="$bgColor"
                                    wrapper-class="w-[80px] sm:w-[100px] min-w-0! sm:mx-auto!"
                                    wireKey="order-active-{{$order->standing_order_id}}-{{$order->is_active}}"
                                    :shadow-color="$shadowColor"
                >
                    <x-slot:options>
                        <x-ui.select.option value="1" text="Active"/>
                        <x-ui.select.option value="0" text="Inactive"/>
                    </x-slot:options>
                </x-ui.select.select>
            </x-form.form-wrapper>
        </div>
        <x-ui.mobile-card-dropdown-menu class="justify-self-end">
            <x-ui.mobile-card-dropdown-link href="{{route('standing-orders.show', compact('order'))}}">View</x-ui.mobile-card-dropdown-link>
            <x-ui.mobile-card-dropdown-link href="{{route('standing-orders.edit', compact('order'))}}">Edit</x-ui.mobile-card-dropdown-link>
            @if(!$order->is_active)
                <x-ui.mobile-card-dropdown-link
                           wire:confirm="Are you sure to delete this standing order?"
                           wire:click="delete({{$order->standing_order_id}})"
                >
                    Delete
                </x-ui.mobile-card-dropdown-link>
            @endif
        </x-ui.mobile-card-dropdown-menu>
    </div>
    {{--card body--}}
    <div class="flex flex-col gap-4">
        <div class="flex justify-between gap-4">
            <div>
                <span class="text-base text-accent">{{$order->customer->customer_name}}</span>
            </div>
            <div>
                <span class="font-semibold">@dayDate($order->start_from)</span>
            </div>
        </div>
    </div>
</x-ui.mobile-card-skeleton>
