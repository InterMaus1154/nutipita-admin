@use(Illuminate\Support\Carbon)
@use(Illuminate\Support\Facades\Request)
@use(Illuminate\Database\Eloquent\Collection as EloquentCollection)
@use(App\Enums\OrderStatus)
@use(App\Models\Order)
@use(Illuminate\Support\Str)
@php
    /**
* @var Order $order
 */
@endphp
<div class="space-y-4">
    <x-success/>
    <x-error/>
    {{--sorting for mobile--}}
    @if($orders->isNotEmpty() && $withMobileSort)
        <x-order.mobile-order-select-sort />
    @endif
    {{--order summary--}}
    @if($withSummaryData)
        <livewire:order.order-summary :orders="$ordersAll" :products="$products" :withIncome="$withIncome"
                                      :visibleByDefault="$summaryVisibleByDefault"/>
    @endif
    @if($orders->isNotEmpty())
        {{--table on desktop--}}
        <div class="hidden sm:block">
            <x-table.table>
                <x-table.head>
                    <x-table.header sortField="is_daytime">
                        #
                    </x-table.header>
                    <x-table.header sortField="customer">
                        Customer
                    </x-table.header>
                    <x-table.header sortField="order_placed_at">
                        Placed Date
                    </x-table.header>
                    <x-table.header sortField="order_due_at">
                        Due Date
                    </x-table.header>
                    <x-table.header sortfield="order_status">
                        Status
                    </x-table.header>
                    @foreach($products as $product)
                        <x-table.header>
                            {{$product->product_name}}
                            <br> {{$product->product_weight_g}}g
                        </x-table.header>
                    @endforeach
                    <x-table.header sortField="total_pita">
                        Total Pita
                    </x-table.header>
                    <x-table.header sortField="total_price">
                        Total £
                    </x-table.header>
                    <x-table.header>
                        Actions
                    </x-table.header>
                </x-table.head>
                <x-table.body>
                    @foreach($orders as $index => $order)
                        @php
                            $orderCount = $orders->total() - ($orders->firstItem() + $index) + 1;
                        @endphp
                        <x-table.row wire:key="order-{{$order->order_id}}">
                            <x-table.data class="whitespace-nowrap space-y-1">
                                @if($order->is_daytime)
                                    <flux:badge color="yellow" variant="solid" size="sm">
                                        <flux:icon.sun class="size-4 text-black"/>
                                    </flux:badge>
                                @endif
                                @if(!$order->is_daytime)
                                    <flux:badge color="violet" variant="solid" size="sm">
                                        <flux:icon.moon class="size-4"/>
                                    </flux:badge>
                                @endif
                                @if($order->is_standing)
                                    <flux:badge color="teal" variant="solid" size="sm">
                                        <flux:icon.arrow-path-rounded-square class="size-4 text-white"/>
                                    </flux:badge>
                                @endif
                                <span class="text-accent">#{{$orderCount}}</span>
                            </x-table.data>
                            <x-table.data>
                                <span class="text-accent text-base">
                                    {{Str::limit($order->customer->customer_name, 20)}}
                                </span>
                            </x-table.data>
                            <x-table.data>
                                @dayDate($order->order_placed_at)
                            </x-table.data>
                            <x-table.data>
                                @dayDate($order->order_due_at)
                            </x-table.data>
                            <x-table.data>
                                <x-order.order-status-select :order="$order"/>
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
                            <x-table.data link>
                                <flux:link href="{{route('orders.edit', compact('order'))}}" title="Edit order">
                                    <flux:icon.pencil-square class="!inline"/>
                                </flux:link>
                                @unless($order->invoice)
                                    <flux:link href="{{route('invoices.create-single', compact('order'))}}"
                                               title="Create invoice">
                                        <flux:icon.clipboard-document-list class="!inline"/>
                                    </flux:link>
                                @else
                                    <flux:link href="{{route('invoices.download', ['invoice' => $order->invoice])}}"
                                               title="Download invoice">
                                        <flux:icon.clipboard-document-check class="!inline"/>
                                    </flux:link>
                                @endunless
                                <flux:link class="cursor-pointer" wire:click="deleteOrder({{$order->order_id}})"
                                           wire:confirm="Are you sure to delete order #{{$order->order_id}} for {{$order->customer->customer_name}}? This action cannot be undone!">
                                    <flux:icon.trash class="!inline"/>
                                </flux:link>
                            </x-table.data>
                        </x-table.row>
                    @endforeach
                </x-table.body>
            </x-table.table>
        </div>
        {{--cards on mobile--}}
        <div class="flex flex-col gap-4 sm:hidden">
            @foreach($orders as $order)
                {{--card wrapper--}}
                <x-order.mobile-order-card wire:key="order-mobile-card-{{$order->order_id}}" :order="$order"/>
            @endforeach
        </div>
    @endif

    {{--bottom pagination--}}
    <div>
        {{$orders->onEachSide(3)->links(data: ['scrollTo' => false])}}
    </div>
</div>
