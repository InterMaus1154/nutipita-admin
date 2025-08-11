<div class="flex flex-col">
    <x-success/>
    <x-error/>
    <x-table.table>
        <x-table.head>
            <th scope="col"
                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                #ID
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                Customer
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                Start From
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                Status
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                Actions
            </th>
        </x-table.head>
        <tbody>
        @forelse($orders as $order)
            <x-table.row>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                    <flux:link href="{{route('standing-orders.show', compact('order'))}}">
                        #{{$order->standing_order_id}}</flux:link>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                    <flux:link
                        href="{{route('customers.show', ['customer' => $order->customer])}}">{{$order->customer->customer_name}}</flux:link>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                    {{daydate($order->start_from)}}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                    @if($order->is_active)
                        <flux:badge color="green">Active</flux:badge>
                    @else
                        <flux:badge color="rose">Inactive</flux:badge>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200 space-x-1.5">
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
                </td>
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
