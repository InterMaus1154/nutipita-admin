@use(Illuminate\Support\Carbon)
@use(Illuminate\Support\Facades\Request)
@use(Illuminate\Database\Eloquent\Collection as EloquentCollection)
@props(['withSummaryData' => true, 'products'])
<div class="space-y-4">
    @if($withSummaryData)
        <x-order-summary :orders="$ordersAll ?? $orders" :products="$products" :withIncome="true"/>
    @endif
    {{--top pagination--}}
    @if($orders instanceof \Illuminate\Pagination\Paginator || $orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div>
            {{$orders->links(data: ['scrollTo' => false])}}
        </div>
    @endif
    <x-table.table>
        <x-table.head>
            <x-table.header>
                #ID
            </x-table.header>
            <x-table.header>
                Customer
            </x-table.header>
            <x-table.header>
                Placed Date
            </x-table.header>
            <x-table.header>
                Due Date
            </x-table.header>
            <x-table.header>
                Status
            </x-table.header>
            @foreach($products as $product)
                <x-table.header>
                    {{$product->product_name}}
                </x-table.header>
            @endforeach
            <x-table.header>
                Total Pita
            </x-table.header>
            <x-table.header>
                Total £
            </x-table.header>
            <x-table.header>
                Actions
            </x-table.header>
        </x-table.head>
        <x-table.body>
            @forelse($orders as $order)
                <x-table.row>
                    <x-table.data>
                        @if($order->is_standing)
                            <flux:badge color="lime" size="lg">S</flux:badge>
                        @endif
                        <flux:link href="{{route('orders.show', compact('order'))}}">
                            #{{$order->order_id}}</flux:link>
                    </x-table.data>
                    <x-table.data>
                        <flux:link
                            href="{{route('customers.show', ['customer' => $order->customer])}}">{{$order->customer->customer_name}}</flux:link>
                    </x-table.data>
                    <x-table.data>
                        @dayDate($order->order_placed_at)
                    </x-table.data>
                    <x-table.data>
                        @dayDate($order->order_due_at)
                    </x-table.data>
                    <x-table.data>
                        @if(str_starts_with($order->order_status, 'Y'))
                            <flux:badge color="amber">{{$order->status}}</flux:badge>
                        @elseif(str_starts_with($order->order_status, 'G'))
                            <flux:badge color="emerald">{{$order->status}}</flux:badge>
                        @else
                            <flux:badge color="red">{{$order->status}}</flux:badge>
                        @endif
                    </x-table.data>
                    @foreach($products as $product)
                        @php
                            $orderProduct = $order->products->firstWhere('product_id', $product->product_id);
                        @endphp
                        <x-table.data>
                            @if($orderProduct)
                                @amountFormat($orderProduct->pivot->product_qty) x
                                @unitPriceFormat($orderProduct->pivot->order_product_unit_price)
                                <br>
                                @moneyFormat($orderProduct->pivot->product_qty *
                                $orderProduct->pivot->order_product_unit_price)
                            @else
                                0
                            @endif
                        </x-table.data>
                    @endforeach
                    <x-table.data>
                        @amountFormat($order->total_pita)
                    </x-table.data>
                    <x-table.data>
                        @moneyFormat($order->total_price)
                    </x-table.data>
                    <x-table.data>
                        <flux:link href="{{route('orders.show', compact('order'))}}">View</flux:link>
                        <flux:link href="{{route('orders.edit', compact('order'))}}">Edit</flux:link>
                        @unless($order->invoice)
                            <flux:link href="{{route('invoices.create-single', compact('order'))}}">
                                Generate Invoice
                            </flux:link>
                        @else
                            <flux:link href="{{route('invoices.download', ['invoice' => $order->invoice])}}">Download
                                Invoice
                            </flux:link>
                        @endunless


                    </x-table.data>
                </x-table.row>
            @empty
                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                    <td class="italic text-lg">No orders found!</td>
                </tr>
            @endforelse
        </x-table.body>
    </x-table.table>
    {{--bottom pagination--}}
    @if($orders instanceof \Illuminate\Pagination\Paginator || $orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div>
            {{$orders->links(data: ['scrollTo' => false])}}
        </div>
    @endif

</div>
