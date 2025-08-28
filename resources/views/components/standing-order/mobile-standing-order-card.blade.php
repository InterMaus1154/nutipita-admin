@props(['order'])
<x-ui.mobile-card-skeleton>
    {{--card header--}}
    <div class="grid grid-cols-[1fr_auto_1fr]">
        {{--badge--}}
        <div class="justify-self-start">
            @php
                $classes = $order->is_active ? "bg-green-500!" : "bg-red-500!";
                $classes.= ' text-black! w-[90px]! px-2! py-2! mx-auto! ';
            @endphp
            <x-form.form-wrapper>
                <x-form.form-select :class="$classes" wire:change="updateOrderStatus({{$order->standing_order_id}}, $event.target.value)">
                    <option value="active" @selected($order->is_active)>Active</option>
                    <option value="inactive" @selected(!$order->is_active)>Inactive</option>
                </x-form.form-select>
            </x-form.form-wrapper>
        </div>
        {{--customer name--}}
        <div class="justify-self-center text-center">
            <span class="text-lg text-accent text-center font-bold">{{$order->customer->customer_name}}</span>
        </div>
        <x-ui.mobile-card-dropdown-menu class="justify-self-end">
            <x-ui.mobile-card-dropdown-link href="{{route('standing-orders.show', compact('order'))}}">View</x-ui.mobile-card-dropdown-link>
            <x-ui.mobile-card-dropdown-link href="{{route('standing-orders.edit', compact('order'))}}">Edit</x-ui.mobile-card-dropdown-link>
        </x-ui.mobile-card-dropdown-menu>
    </div>
    {{--card body--}}
    <div class="flex flex-col gap-4">
        <div class="flex gap-2 items-center">
            <flux:icon.circle-play class="size-5 text-accent"/>
            <span>@dayDate($order->start_from)</span>
        </div>
    </div>
</x-ui.mobile-card-skeleton>
