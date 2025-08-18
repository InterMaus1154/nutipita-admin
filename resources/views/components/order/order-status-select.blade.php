@props(['order'])
@use(App\Enums\OrderStatus)
<div class="cursor-pointer">
    @php
        // match color
        if($order->order_status === OrderStatus::G_PAID->name){
            $bgColor = "bg-green-500!";
        }else if($order->order_status === OrderStatus::Y_CONFIRMED->name){
            $bgColor = "bg-orange-500!";
        }else if($order->order_status === OrderStatus::O_DELIVERED_UNPAID->name){
            $bgColor = "bg-red-500!";
        }
        $classes = "$bgColor text-black! w-[110px]! px-2! py-2! mx-auto!";
    @endphp
    <x-form.form-wrapper>
        <x-form.form-select :class="$classes"
                            wire:change="updateOrderStatus({{$order->order_id}}, $event.target.value)">
            @foreach(OrderStatus::cases() as $status)
                <option
                    @selected($order->order_status === $status->name) wire:key="order-status-{{$order->order_id}}"
                    value="{{$status->name}}">{{ucfirst($status->value)}}</option>
            @endforeach
        </x-form.form-select>
    </x-form.form-wrapper>
</div>
