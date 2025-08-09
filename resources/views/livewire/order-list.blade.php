@use(Illuminate\Support\Carbon)
@use(Illuminate\Support\Facades\Request)
@use(Illuminate\Database\Eloquent\Collection as EloquentCollection)
@props(['withSummaryData' => true, 'products', 'summaryVisibleByDefault' => true, 'withSummaryPdf' => false])
@php
    /**
* @var \App\Models\Order $order
 */
@endphp
<div class="space-y-4" x-data x-init="initComponent()">
    <x-success/>
    <x-error/>
    {{--sorting for mobile--}}
    <div class="sm:hidden">
        <x-form.form-label id="mobile_sort" text="Sort by"/>
        <x-form.form-select id="mobile_sort" wireModelLive="mobileSort">
            <option value="desc:order_id">Order Id (desc) [default]</option>
            <option value="asc:order_id">Order Id (asc)</option>
            <option value="desc:customer">Customer Name (desc)</option>
            <option value="asc:customer">Customer Name (asc)</option>
            <option value="desc:order_placed_at">Placed At (desc)</option>
            <option value="asc:order_placed_at">Placed At (asc)</option>
            <option value="desc:order_due_at">Due At (desc)</option>
            <option value="asc:order_due_at">Due At (asc)</option>
            <option value="desc:order_status">Status (desc)</option>
            <option value="asc:order_status">Status (asc)</option>
            <option value="desc:total_pita">Total Pita (desc)</option>
            <option value="asc:total_pita">Total Pita (asc)</option>
            <option value="desc:total_price">Total Price (desc)</option>
            <option value="asc:total_price">Total Price (asc)</option>
        </x-form.form-select>
    </div>
    {{--order summary--}}
    @if($withSummaryData)
        <livewire:order-summary :orders="$ordersAll ?? $orders" :products="$products" :withIncome="true"
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
    {{--table on desktop--}}
    <div class="hidden sm:block">
        <x-table.table>
            <x-table.head>
                <x-table.header wire:click="setSort('order_id')">
                    <div class="flex items-center justify-center text-center cursor-pointer">
                        #ID
                        <flux:icon.arrows-up-down variant="mini"/>
                    </div>
                </x-table.header>
                <x-table.header wire:click="setSort('customer')">
                    <div class="flex items-center justify-center text-center cursor-pointer">
                        Customer
                        <flux:icon.arrows-up-down variant="mini"/>
                    </div>
                </x-table.header>
                <x-table.header wire:click="setSort('order_placed_at')">
                    <div class="flex items-center justify-center text-center cursor-pointer">
                        Placed Date
                        <flux:icon.arrows-up-down variant="mini"/>
                    </div>
                </x-table.header>
                <x-table.header wire:click="setSort('order_due_at')">
                    <div class="flex items-center justify-center text-center cursor-pointer">
                        Due Date
                        <flux:icon.arrows-up-down variant="mini"/>
                    </div>
                </x-table.header>
                <x-table.header wire:click="setSort('order_status')">
                    <div class="flex items-center justify-center cursor-pointer">
                        Status
                        <flux:icon.arrows-up-down variant="mini"/>
                    </div>
                </x-table.header>
                @foreach($products as $product)
                    <x-table.header>
                        {{$product->product_name}} {{$product->product_weight_g}}g
                    </x-table.header>
                @endforeach
                <x-table.header wire:click="setSort('total_pita')">
                    <div class="flex items-center justify-center text-center cursor-pointer">
                        Total Pita
                        <flux:icon.arrows-up-down variant="mini"/>
                    </div>
                </x-table.header>
                <x-table.header wire:click="setSort('total_price')">
                    <div class="flex items-center justify-center text-center cursor-pointer">
                        Total £
                        <flux:icon.arrows-up-down variant="mini"/>
                    </div>
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
                                href="{{route('customers.show', ['customer' => $order->customer])}}">
                                {{\Illuminate\Support\Str::limit($order->customer->customer_name, 20)}}
                            </flux:link>
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
    </div>
    {{--cards on mobile--}}
    <div class="flex flex-col gap-4 sm:hidden">
        @foreach($orders as $order)
            {{--card wrapper--}}
            <div class="flex flex-col gap-4 rounded-sm shadow-sm border-1 border-gray-600 p-4">
                {{--card header--}}
                <div class="flex gap-4 justify-between">
                    {{--status badges--}}
                    <div class="flex gap-2">
                        {{--normal status badge--}}
                        <div class="cursor-pointer" wire:click="openStatusUpdateModal({{$order->order_id}})">
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
                        </div>
                        {{--daytime badge--}}
                        @if($order->is_daytime)
                            <flux:badge color="cyan">Daytime</flux:badge>
                        @endif
                        {{--standing order badge--}}
                        @if($order->is_standing)
                            <flux:badge color="lime">Standing</flux:badge>
                        @endif
                    </div>
                    {{--dropdown menu for actions--}}
                    <div class="relative">
                        <flux:icon.adjustments-horizontal class="openActionMenuButton"
                                                          data-order-id="{{$order->order_id}}"/>
                        {{--actual link box--}}
                        <div
                            class="hidden absolute z-100 left-[-8rem] top-6 flex-col gap-4 dark:bg-gray-900 border-1 border-gray-600 p-2 min-w-[180px] action"
                            data-order-id="{{$order->order_id}}">
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
                        </div>
                    </div>
                </div>
                {{--customer and due date info--}}
                <div class="flex justify-between gap-4 items-center">
                    <flux:link href="{{route('customers.show', ['customer' => $order->customer])}}">
                        {{$order->customer->customer_name}}
                    </flux:link>
                    <div class="flex gap-2">
                        <span class="text-base font-semibold">
                            @dayDate($order->order_due_at)
                        </span>
                    </div>
                </div>
                {{--total pita and price info--}}
                <div class="flex justify-between gap-4 items-center flex-wrap">
                    <div class="flex gap-2">
                        <flux:badge color="indigo">Pita:</flux:badge>
                        <span class="text-base">@amountFormat($order->total_pita)</span>
                    </div>
                    <div class="flex gap-2">
                        <flux:badge color="indigo">£:</flux:badge>
                        <span class="text-base">@moneyFormat($order->total_price)</span>
                    </div>
                </div>
                {{--extra info section wrapper--}}
                <div class="flex flex-col gap-4">
                    {{--extra info section--}}
                    <div class="flex-col gap-4 hidden" id="extra-info-{{$order->order_id}}">
                        <div class="flex gap-2">
                            <flux:badge color="indigo">Placed:</flux:badge>
                            <span class="text-base">@dayDate($order->order_placed_at)</span>
                        </div>
                        {{--product wrapper--}}
                        <div class="flex flex-col gap-4">
                            @foreach($order->products as $orderProduct)
                                <div class="text-base flex gap-4 justify-between items-center">
                                    <span>{{$orderProduct->product_name}}</span>
                                    <div class="flex flex-col gap-1 justify-center items-center text-center">
                                        <span>@amountFormat($orderProduct->pivot->product_qty) x @unitPriceFormat($orderProduct->pivot->order_product_unit_price)</span>
                                        <span>@moneyFormat($orderProduct->pivot->product_qty * $orderProduct->pivot->order_product_unit_price)</span>
                                    </div>
                                </div>
                                <flux:separator />
                            @endforeach
                        </div>
                    </div>
                    <flux:button data-order-id="{{$order->order_id}}" class="openExtraInfoButton">
                        <flux:icon.chevron-double-down />
                    </flux:button>
                </div>
            </div>
        @endforeach
    </div>
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
