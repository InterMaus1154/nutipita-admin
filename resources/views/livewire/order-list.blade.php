@use(Illuminate\Support\Carbon)
@use(Illuminate\Support\Facades\Request)
<div>
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
                                Customer
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Placed Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Due Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Status
                            </th>
                            @foreach($products as $product)
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    {{$product->product_name}}
                                </th>
                            @endforeach
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Total Pita
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Total £
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($orders as $order)
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200 space-x-1">
                                    @if($order->is_standing)
                                        <flux:badge color="lime" size="lg">S</flux:badge>
                                    @endif
                                    <flux:link href="{{route('orders.show', compact('order'))}}">
                                        #{{$order->order_id}}</flux:link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    <flux:link
                                        href="{{route('customers.show', ['customer' => $order->customer])}}">{{$order->customer->customer_name}}</flux:link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    {{dayDate($order->order_placed_at)}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    {{dayDate($order->order_due_at)}}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    @if(str_starts_with($order->order_status, 'Y'))
                                        <flux:badge color="amber">{{$order->status}}</flux:badge>
                                    @elseif(str_starts_with($order->order_status, 'G'))
                                        <flux:badge color="emerald">{{$order->status}}</flux:badge>
                                    @else
                                        <flux:badge color="red">{{$order->status}}</flux:badge>
                                    @endif
                                </td>
                                @foreach($products as $product)
                                    @php
                                        $orderProduct = $order->products->firstWhere('product_id', $product->product_id);
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        @if($orderProduct)
                                            {{$orderProduct->pivot->product_qty}} x
                                            £{{$orderProduct->pivot->order_product_unit_price}}
                                            <br>
                                            £{{$orderProduct->pivot->product_qty * $orderProduct->pivot->order_product_unit_price}}
                                        @else
                                            0
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    {{$order->total_pita}}
                                </td>
                                <td>{{$order->total_price_format}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-1.5">
                                    <flux:link href="{{route('orders.edit', compact('order'))}}">Edit</flux:link>
{{--                                    @if(isset($onOrderIndex) && $onOrderIndex)--}}
{{--                                        <flux:link wire:confirm="Are you sure you want to delete this order?"--}}
{{--                                            wire:click="delete({{$order->order_id}})"--}}
{{--                                        >Delete</flux:link>--}}
{{--                                    @endif--}}
                                    <flux:link href="{{route('invoices.create-single', compact('order'))}}">Generate Invoice</flux:link>
                                </td>
                            </tr>
                        @empty
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="italic text-lg">No orders found!</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
