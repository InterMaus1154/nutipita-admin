<div class="flex flex-col">
    <x-success/>
    <x-error/>
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
                    <span class="text-accent">
                        {{$order->customer->customer_name}}
                    </span>
                </x-table.data>
                <x-table.data>
                    {{daydate($order->start_from)}}
                </x-table.data>
                <x-table.data class="cursor-pointer">
                    @php
                        $classes = $order->is_active ? "bg-green-400/50!" : "bg-red-400/50!";
                        $classes.= ' text-white! max-w-fit! mx-auto! ';
                    @endphp
                    <x-form.form-wrapper>
                        <x-form.form-select :class="$classes" wire:change="updateOrderStatus({{$order->standing_order_id}}, $event.target.value)">
                            <option value="active" @selected($order->is_active)>Active</option>
                            <option value="inactive" @selected(!$order->is_active)>Inactive</option>
                        </x-form.form-select>
                    </x-form.form-wrapper>
                </x-table.data>
                <x-table.data>
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
