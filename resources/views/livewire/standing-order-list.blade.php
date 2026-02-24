<div class="flex flex-col">
    <x-success/>
    <x-error/>
    <div class="hidden sm:block">
        <x-table.table smallTable>
            <x-table.head>
                <x-table.header sortField="standing_order_id">
                    #ID
                </x-table.header>
                <x-table.header sortField="customer">
                    Customer
                </x-table.header>
                <x-table.header sortField="start_from">
                    Start From
                </x-table.header>
                <x-table.header sortField="is_active">
                    Status
                </x-table.header>
                <x-table.header>
                    Actions
                </x-table.header>
            </x-table.head>
            <tbody>
            @forelse($orders as $order)
                <x-table.row wire:key="standing-order-{{$order->standing_order_id}}">
                    <x-table.data>
                        <flux:link href="{{route('standing-orders.show', compact('order'))}}">
                            #{{$order->standing_order_id}}</flux:link>
                    </x-table.data>
                    <x-table.data>
                    <span class="text-accent text-base">
                        {{$order->customer->customer_name}}
                    </span>
                    </x-table.data>
                    <x-table.data>
                        {{daydate($order->start_from)}}
                    </x-table.data>
                    <x-table.data class="cursor-pointer">
                        @php
                            $classes = $order->is_active ? "bg-green-500!" : "bg-red-500!";
                            $classes.= ' text-black! w-[90px]! px-2! py-2! mx-auto! ';
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
                    </x-table.data>
                    <x-table.data link>
                        <flux:link href="{{route('standing-orders.show', compact('order'))}}">
                            <flux:icon.eye class="!inline"/>
                        </flux:link>
                        <flux:link href="{{route('standing-orders.edit', compact('order'))}}">
                            <flux:icon.pencil-square class="!inline"/>
                        </flux:link>
                        @if(!$order->is_active)
                            <flux:link class="cursor-pointer"
                                       wire:confirm="Are you sure to delete this standing order?"
                                       wire:click="delete({{$order->standing_order_id}})"
                            >
                                <flux:icon.trash class="!inline"/>
                            </flux:link>
                        @endif
                    </x-table.data>
                </x-table.row>
            @empty
                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                        No orders found!
                    </td>
                </tr>
            @endforelse
            </tbody>
        </x-table.table>
    </div>
    {{--mobile cards--}}
    <div class="flex flex-col gap-4 sm:hidden">
        @foreach($orders as $order)
            <x-standing-order.mobile-standing-order-card
                    wire:key="standing-order-mobile-card-{{$order->standing_order_id}}" :order="$order"/>
        @endforeach
    </div>
</div>
