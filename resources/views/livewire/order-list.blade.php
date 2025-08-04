@use(Illuminate\Support\Carbon)
@use(Illuminate\Support\Facades\Request)
@use(Illuminate\Database\Eloquent\Collection as EloquentCollection)
@props(['withSummaryData' => true, 'products', 'summaryVisibleByDefault' => true, 'withSummaryPdf' => false])
<div class="space-y-4">
    @if($orders->isNotEmpty())
        <div class="space-y-4">
            <x-success/>
            <x-error/>
            {{--order summary--}}
            @if($withSummaryData)
                <x-order-summary :orders="$ordersAll ?? $orders" :products="$products" :withIncome="true"
                                 :visibleByDefault="$summaryVisibleByDefault"/>
            @endif
            @if($withSummaryPdf && isset($filters['customer_id']))
                <flux:link href="{{$this->getOrderSummaryPdfUrl()}}" class="cursor-pointer">
                    Download order summary for selected orders (PDF)
                </flux:link>
            @endif
            {{--top pagination--}}
            <div>
                {{$orders->onEachSide(3)->links(data: ['scrollTo' => false])}}
            </div>
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
                            {{$product->product_name}} {{$product->product_weight_g}}g
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
                        <x-table.row wire:key="order-{{$order->order_id}}">
                            <x-table.data>
                                @if($order->is_standing)
                                    <flux:badge color="lime" size="lg">S</flux:badge>
                                @endif
                                @if($order->is_daytime)
                                    <flux:badge color="cyan" size="lg">D</flux:badge>
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
                            <x-table.data wire:click="openStatusUpdateModal({{$order->order_id}})"
                                          class="cursor-pointer">
                                @if(str_starts_with($order->order_status, 'Y'))
                                    <flux:badge color="amber">{{$order->status}}
                                        <flux:icon.chevron-down variant="mini"/>
                                    </flux:badge>
                                @elseif(str_starts_with($order->order_status, 'G'))
                                    <flux:badge color="emerald">{{$order->status}}
                                        <flux:icon.chevron-down variant="mini"/>
                                    </flux:badge>
                                @elseif(str_starts_with($order->order_status, 'O'))
                                    <flux:badge color="orange">{{$order->status}}
                                        <flux:icon.chevron-down variant="mini"/>
                                    </flux:badge>
                                @else
                                    <flux:badge color="red">{{$order->status}}
                                        <flux:icon.chevron-down variant="mini"/>
                                    </flux:badge>
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
                                    <flux:link href="{{route('invoices.download', ['invoice' => $order->invoice])}}">
                                        Download
                                        Invoice
                                    </flux:link>
                                @endunless
                                <flux:link class="cursor-pointer" wire:click="deleteOrder({{$order->order_id}})"
                                           wire:confirm="Are you sure to delete order # {{$order->order_id}} ? This action cannot be undone!">
                                    Delete
                                </flux:link>
                            </x-table.data>
                        </x-table.row>
                    @empty
                        <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                            <td class="italic text-lg">No orders!</td>
                        </tr>
                    @endforelse
                </x-table.body>
            </x-table.table>
            {{--bottom pagination--}}
            <div>
                {{$orders->onEachSide(3)->links(data: ['scrollTo' => false])}}
            </div>
            {{--status update modal--}}
            <div @class(['hidden' => !$isStatusUpdateModalVisible,
'fixed inset-0 z-50 flex items-center justify-center'])>
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black opacity-50"></div>

                <!-- Modal Content -->
                <div
                    class="relative z-10 w-full max-w-md p-6 bg-white dark:bg-zinc-800 rounded-lg shadow-lg dark:text-white text-black flex flex-col gap-4">
                    <div class="flex flex-col gap-4">
                        <div class="flex gap-4 justify-between">
                            <h2 class="text-xl font-semibold mb-4 text-center">Update Order Status</h2>
                            <flux:button wire:click="closeStatusUpdateModal">X</flux:button>
                        </div>
                        <flux:separator/>
                    </div>
                    <x-form.form-wrapper>
                        <x-form.form-label id="order_status_update" text="Select a status"/>
                        <x-form.form-select id="order_status_update" wireModelLive="updateOrderStatusName">
                            @foreach(\App\Enums\OrderStatus::cases() as $status)
                                <option
                                    value="{{$status->name}}" @selected($modalSelectedOrder && $modalSelectedOrder->order_status === $status->name)>
                                    {{ucfirst($status->value)}}
                                </option>
                            @endforeach
                        </x-form.form-select>
                    </x-form.form-wrapper>
                </div>
            </div>
        </div>
    @else
        <em>No orders found for the current filter!</em>
    @endif
</div>
