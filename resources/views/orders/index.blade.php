<x-layout>
    <section class="space-y-4">
        <flux:heading size="xl" level="1">Orders</flux:heading>
        <flux:link variant="ghost" href="{{route('orders.create')}}" class="!block">Add new order</flux:link>
        <flux:separator/>
        <x-success/>
        {{--orders table on large--}}
        <div class="hidden lg:flex flex-col ">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 text-center">
                            <thead>
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    ID
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Customer
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Placed At
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Due At
                                </th>
                                @foreach($products as $product)
                                    <th scope="col"
                                        class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                        {{$product->product_name}}
                                    </th>
                                @endforeach
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Total Pita
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Total £
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($orders as $order)
                                <tr class="odd:bg-white even:bg-gray-100 dark:odd:bg-neutral-900 dark:even:bg-neutral-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        <flux:link href="{{route('orders.show', compact('order'))}}">
                                            #{{$order->order_id}}
                                        </flux:link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        <flux:link
                                            href="{{route('customers.show', ['customer' => $order->customer])}}">{{$order->customer->customer_name}}
                                        </flux:link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        @if(str_starts_with($order->order_status, 'Y'))
                                            <flux:badge color="yellow">{{$order->status}}</flux:badge>
                                        @elseif(str_starts_with($order->order_status,'G'))
                                            <flux:badge color="green">{{$order->status}}</flux:badge>
                                        @elseif(str_starts_with($order->order_status, 'R'))
                                            <flux:badge color="red">{{$order->status}}</flux:badge>
                                        @else
                                            <flux:badge color="zinc">{{$order->status}}</flux:badge>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        {{$order->order_placed_at}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        {{$order->order_due_at ?? "-"}}
                                    </td>
                                    @foreach($products as $product)
                                        @php
                                            $orderProduct = $order->products->firstWhere('product_id', $product->product_id);
                                        @endphp
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                            @if($orderProduct)
                                                {{$orderProduct->pivot->product_qty}} x
                                                £{{$orderProduct->pivot->order_product_unit_price}} <br>
                                                £{{$orderProduct->pivot->product_qty * $orderProduct->pivot->order_product_unit_price}}
                                            @else
                                                0
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        {{$order->total_pita}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        £{{$order->total_price}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        <flux:link
                                            href="{{route('orders.edit', compact('order'))}}">Edit Order
                                        </flux:link>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{--order cards on small--}}
        <div class="flex flex-col gap-4 lg:hidden">
            @foreach($orders as $order)
                <div
                    class="flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl dark:bg-neutral-900 dark:border-neutral-700 dark:shadow-neutral-700/70">
                    <div class="p-4 md:p-7 flex flex-col gap-4 items-start">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                            <flux:link href="{{route('orders.show', compact('order'))}}">
                                #{{$order->order_id}}
                            </flux:link>
                            for
                            <flux:link
                                href="{{route('customers.show', ['customer' => $order->customer])}}">{{$order->customer->customer_name}}
                            </flux:link>
                        </h3>
                        @if(str_starts_with($order->order_status, 'Y'))
                            <flux:badge color="yellow">{{$order->status}}</flux:badge>
                        @elseif(str_starts_with($order->order_status,'G'))
                            <flux:badge color="green">{{$order->status}}</flux:badge>
                        @elseif(str_starts_with($order->order_status, 'R'))
                            <flux:badge color="red">{{$order->status}}</flux:badge>
                        @else
                            <flux:badge color="zinc">{{$order->status}}</flux:badge>
                        @endif
                        <div class="flex gap-6 w-full">
                            <div class="flex flex-col gap-2">
                                <p class="text-xl">Placed At</p>
                                <p class="text-xl">{{$order->order_placed_at}}</p>
                            </div>
                            <div class="flex flex-col gap-2">
                                <p class="text-xl">Due At</p>
                                <p class="text-xl">{{$order->order_placed_at ?? "-"}}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            @foreach($products as $product)
                                <div class="flex flex-col gap-2 p-4 items-center border border-gray-200 rounded-md">
                                    <p class="text-base">{{$product->product_name}}</p>
                                    @php
                                        $orderProduct = $order->products->firstWhere('product_id', $product->product_id);
                                    @endphp
                                    @if($orderProduct)
                                        <p class="text-center text-xl">
                                            {{$orderProduct->pivot->product_qty}} x
                                            £{{$orderProduct->pivot->order_product_unit_price}} <br>
                                            £{{$orderProduct->pivot->product_qty * $orderProduct->pivot->order_product_unit_price}}
                                        </p>
                                    @else
                                        <p class="text-center text-xl">0</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xl">Total pita: {{$order->total_pita}}</p>
                        <p class="text-xl">Total price: £{{$order->total_price}}</p>
                        <flux:link href="{{route('orders.edit', compact('order'))}}" class="!flex items-center gap-1">
                            Edit Order
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"></path>
                            </svg>
                        </flux:link>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-layout>
