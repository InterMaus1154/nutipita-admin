<div class="flex flex-col">
    <x-success/>
    <x-error/>
    <x-table.table>
        <x-table.head>
            <x-table.header>
                #ID
            </x-table.header>
            <x-table.header>
                Customer
            </x-table.header>
            <x-table.header>
                Start From
            </x-table.header>
            <x-table.header>
                Status
            </x-table.header>
            <x-table.header>
                Actions
            </x-table.header>
        </x-table.head>
        <tbody>
        @forelse($orders as $order)
            <x-table.row>
                <x-table.data>
                    <flux:link href="{{route('standing-orders.show', compact('order'))}}">
                        #{{$order->standing_order_id}}</flux:link>
                </x-table.data>
                <x-table.data>
                    <flux:link
                        href="{{route('customers.show', ['customer' => $order->customer])}}">{{$order->customer->customer_name}}</flux:link>
                </x-table.data>
                <x-table.data>
                    {{daydate($order->start_from)}}
                </x-table.data>
                <x-table.data wire:click="openStatusUpdateModal({{$order}})" class="cursor-pointer">
                    @if($order->is_active)
                        <flux:badge color="green">Active
                            <flux:icon.chevron-down variant="mini"/>
                        </flux:badge>
                    @else
                        <flux:badge color="rose">Inactive
                            <flux:icon.chevron-down variant="mini"/>
                        </flux:badge>
                    @endif
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
    {{--status update modal--}}
    <div @class(['hidden' => !$isStatusUpdateModalVisible,
            'fixed inset-0 z-50 flex items-center justify-center'])>
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <!-- Modal Content -->
        <div
            class="relative z-10 w-full max-w-md p-6 bg-white dark:bg-zinc-800 rounded-lg shadow-lg dark:text-white text-black flex flex-col gap-4" x-data x-on:click.outside="$wire.closeStatusUpdateModal()">
            <div class="flex flex-col gap-4">
                <div class="flex gap-4 justify-between">
                    <h2 class="text-xl font-semibold mb-4 text-center">Update Status</h2>
                    <flux:button wire:click="closeStatusUpdateModal">X</flux:button>
                </div>
                <flux:separator/>
            </div>
            <div class="mx-auto">
                <x-form.form-wrapper>
                    <x-form.form-label id="order_status_update" text="Select a status"/>
                    <x-form.form-select id="order_status_update" wireModelLive="updateOrderStatus">
                        <option value="active" @selected($selectedOrder && $selectedOrder->is_active)>Active</option>
                        <option value="inactive" @selected($selectedOrder && !$selectedOrder->is_active)>Inactive</option>
                    </x-form.form-select>
                </x-form.form-wrapper>
            </div>
        </div>
    </div>
</div>
