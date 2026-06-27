@props(['order_id', 'order_status'])
@use(App\Enums\OrderStatus)
<div {{$attributes->merge([
    'class' => 'cursor_pointer'
])}}>
    @php
        $bgColor = '';
        $shadowColor = '';
            if($order_status === OrderStatus::G_PAID->name){
                $bgColor = "bg-green-500!";
                $shadowColor = "oklch(72.3% 0.219 149.579)";
            }else if($order_status === OrderStatus::Y_CONFIRMED->name){
                $bgColor = "bg-orange-400!";
                $shadowColor = "oklch(75% 0.183 55.934)";
            }else if($order_status === OrderStatus::O_DELIVERED_UNPAID->name){
                $bgColor = "bg-red-500!";
                $shadowColor = "oklch(63.7% 0.237 25.331)";
            }
    @endphp
    <x-form.form-wrapper>
        <x-ui.select.select wire-change="updateOrderStatus"
                            :wire-change-prop="$order_id"
                            :pre-selected-value="$order_status"
                            inner-class="text-black text-sm! outline-0!"
                            :bg="$bgColor"
                            wrapper-class="w-[80px] sm:w-[100px] min-w-0! sm:mx-auto!"
                            wireKey="order-status-{{$order_id}}-{{$order_status}}"
                            :shadow-color="$shadowColor"

        >
            <x-slot:options>
                @foreach(OrderStatus::cases() as $status)
                    <x-ui.select.option :value="$status->name" :text="$status->value"/>
                @endforeach
            </x-slot:options>
        </x-ui.select.select>
    </x-form.form-wrapper>
</div>
