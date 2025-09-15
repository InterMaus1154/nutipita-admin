@aware(['order'])
@use(App\Enums\OrderStatus)
<div {{$attributes->merge([
    'class' => 'cursor_pointer'
])}}>
    @php
        // match color
        if($order->order_status === OrderStatus::G_PAID->name){
            $bgColor = "bg-green-500!";
        }else if($order->order_status === OrderStatus::Y_CONFIRMED->name){
            $bgColor = "bg-orange-400!";
        }else if($order->order_status === OrderStatus::O_DELIVERED_UNPAID->name){
            $bgColor = "bg-red-500!";
        }
//        $classes = "$bgColor text-black! w-[110px]! px-2! py-2! mx-auto!";
    @endphp
    <x-form.form-wrapper>
        <x-ui.select.select wire-change="updateOrderStatus" :wire-change-prop="$order->order_id"
                            :pre-selected-value="$order->order_status" inner-class="text-black text-sm!" :bg="$bgColor"
                            wrapper-class="w-[80px] min-w-0!"
                            placeholder="Pl"
                            wireKey="order-status-{{$order->order_id}}"
        >
            <x-slot:options>
                @foreach(OrderStatus::cases() as $status)
                    <x-ui.select.option :value="$status->name" :text="$status->value"/>
                @endforeach
            </x-slot:options>
        </x-ui.select.select>
    </x-form.form-wrapper>
</div>
