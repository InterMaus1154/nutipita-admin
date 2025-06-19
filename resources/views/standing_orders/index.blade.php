<x-flux-layout>
    <x-page-section >
        <x-page-heading title="Standing Orders"/>
        <x-success/>
        <x-error />
        <flux:link href="{{route('standing-orders.create')}}">Add new standing order</flux:link>
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead>
                            <tr>
                                <th scope="col" class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">#ID</th>
                                <th scope="col" class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">Customer</th>
                                <th scope="col" class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">Start From</th>
                                <th scope="col" class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        <flux:link href="{{route('standing-orders.show', compact('order'))}}">#{{$order->standing_order_id}}</flux:link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        <flux:link href="{{route('customers.show', ['customer' => $order->customer])}}">{{$order->customer->customer_name}}</flux:link>
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
                                        <flux:link href="{{route('standing-orders.show', compact('order'))}}">View</flux:link>
                                        <flux:link href="{{route('standing-orders.edit', compact('order'))}}">Edit</flux:link>
                                    </td>
                                </tr>
                            @empty
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">No orders found!</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </x-page-section>
</x-flux-layout>
