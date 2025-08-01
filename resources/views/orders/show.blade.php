<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Order #{{$order->order_id}}"/>
        <x-success/>
        <x-error/>
        <flux:link href="{{route('orders.edit', compact('order'))}}">Edit Order</flux:link>
        @if($order->is_standing)
            <div class="flex items-start">
                <flux:badge color="lime" size="lg">Standing Order</flux:badge>
            </div>
        @endif
        @if($order->is_daytime)
            <div class="flex items-start">
                <flux:badge color="cyan" size="lg">Daytime Order</flux:badge>
            </div>
        @endif
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="divide-y divide-gray-200 dark:divide-neutral-700 border border-zinc-600 border-solid">
                            <tbody>
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        Customer
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        <flux:link href="{{route('customers.show', ['customer' => $order->customer])}}">{{$order->customer->customer_name}}</flux:link>
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        Placed At
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        {{dayDate($order->order_placed_at)}}
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        Due At
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        {{dayDate($order->order_due_at)}}
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        Status
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        @if(str_starts_with($order->order_status, 'Y'))
                                            <flux:badge color="amber">{{$order->status}}</flux:badge>
                                        @elseif(str_starts_with($order->order_status, 'G'))
                                            <flux:badge color="emerald">{{$order->status}}</flux:badge>
                                        @else
                                            <flux:badge color="red">{{$order->status}}</flux:badge>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        Total Pita
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        {{$order->total_pita}}
                                    </td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        Total Price
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        £{{$order->total_price}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <h3 class="font-bold">Order's Products</h3>
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead>
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    #ID
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Name
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Unit Price
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Quantity
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Total Price
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->products as $product)
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        <flux:link href="{{route('products.show', compact('product'))}}">{{$product->product_id}}</flux:link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        <flux:link href="{{route('products.show', compact('product'))}}">{{$product->product_name}}</flux:link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        £{{$product->pivot->order_product_unit_price}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        {{$product->pivot->product_qty}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        £{{$product->pivot->product_qty * $product->pivot->order_product_unit_price}}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </x-page-section>
</x-flux-layout>
