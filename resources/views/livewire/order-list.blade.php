@use(Illuminate\Support\Carbon)
@use(Illuminate\Support\Facades\Request)
@use(Illuminate\Database\Eloquent\Collection as EloquentCollection)
@use(App\Enums\OrderStatus)
@use(App\Models\Order)
@props(['withSummaryData' => true, 'products', 'summaryVisibleByDefault' => true, 'withSummaryPdf' => false])
@php
    /**
* @var Order $order
 */
@endphp
<div class="space-y-4">
    <x-success/>
    <x-error/>
    {{--sorting for mobile--}}
    @if($orders->isNotEmpty())
        <div class="sm:hidden">
            <x-form.form-label id="mobile_sort" text="Sort by"/>
            <x-form.form-select id="mobile_sort" wireModelLive="mobileSort">
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
    @endif
    {{--order summary--}}
    @if($withSummaryData)
        <livewire:order-summary :orders="$ordersAll" :products="$products" :withIncome="true"
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
                                    {{\Illuminate\Support\Str::limit($order->customer->customer_name, 20)}}
                                </span>
                            </x-table.data>
                            <x-table.data>
                                @dayDate($order->order_placed_at)
                            </x-table.data>
                            <x-table.data>
                                @dayDate($order->order_due_at)
                            </x-table.data>
                            <x-table.data>
                                @php
                                    // match color
                                    if($order->order_status === OrderStatus::G_PAID->name){
                                        $bgColor = "bg-lime-400!";
                                    }else if($order->order_status === OrderStatus::Y_CONFIRMED->name){
                                        $bgColor = "bg-orange-400!";
                                    }else if($order->order_status === OrderStatus::O_DELIVERED_UNPAID->name){
                                        $bgColor = "bg-red-400!";
                                    }
                                    $classes = "$bgColor text-black! w-[110px]! px-2! py-2! mx-auto!";
                                @endphp
                                <x-form.form-wrapper>
                                    <x-form.form-select :class="$classes"
                                                        wire:change="updateOrderStatus({{$order->order_id}}, $event.target.value)">
                                        @foreach(OrderStatus::cases() as $status)
                                            <option @selected($order->order_status === $status->name) wire:key="order-status-{{$order->order_id}}"
                                                    value="{{$status->name}}">{{ucfirst($status->value)}}</option>
                                        @endforeach
                                    </x-form.form-select>
                                </x-form.form-wrapper>
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
                <div wire:key="order-{{$order->order_id}}"
                     class="flex flex-col gap-4 rounded-sm shadow-sm border-1 border-neutral-700 p-4"
                     x-data="{ actionMenuOpen: false, detailsMenuOpen: false}">
                    {{--card header--}}
                    <div class="flex gap-4 justify-between">
                        {{--status badges--}}
                        <div class="flex gap-2">
                            {{--normal status badge--}}
                            <div class="cursor-pointer" >
                                @php
                                    // match color
                                    if($order->order_status === OrderStatus::G_PAID->name){
                                        $bgColor = "bg-lime-400!";
                                    }else if($order->order_status === OrderStatus::Y_CONFIRMED->name){
                                        $bgColor = "bg-orange-400!";
                                    }else if($order->order_status === OrderStatus::O_DELIVERED_UNPAID->name){
                                        $bgColor = "bg-red-400!";
                                    }
                                    $classes = "$bgColor text-black! w-[110px]! px-2! py-2! mx-auto!";
                                @endphp
                                <x-form.form-wrapper>
                                    <x-form.form-select :class="$classes"
                                                        wire:change="updateOrderStatus({{$order->order_id}}, $event.target.value)">
                                        @foreach(OrderStatus::cases() as $status)
                                            <option @selected($order->order_status === $status->name) wire:key="order-status-{{$order->order_id}}"
                                                    value="{{$status->name}}">{{ucfirst($status->value)}}</option>
                                        @endforeach
                                    </x-form.form-select>
                                </x-form.form-wrapper>
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
                            <flux:icon.adjustments-horizontal x-on:click="actionMenuOpen = !actionMenuOpen"/>
                            {{--actual link box--}}
                            <div x-show="actionMenuOpen" x-on:click.outside="actionMenuOpen = false" x-cloak x-transition
                                 class="absolute z-100 left-[-9rem] top-5 border-2 border-neutral-700 rounded-xl bg-zinc-800 p-4 flex flex-col gap-4 min-w-[200px] action">
                                <flux:link class="py-1 px-4 rounded-sm hover:bg-neutral-500/50 " href="{{route('orders.show', compact('order'))}}">View</flux:link>
                                <flux:link class="py-1 px-4 rounded-sm hover:bg-neutral-500/50 " href="{{route('orders.edit', compact('order'))}}">Edit</flux:link>
                                @unless($order->invoice)
                                    <flux:link class="py-1 px-4 rounded-sm hover:bg-neutral-500/50! block! " href="{{route('invoices.create-single', compact('order'))}}">
                                        Generate Invoice
                                    </flux:link>
                                @else
                                    <flux:link class="py-1 px-4 rounded-sm hover:bg-neutral-500/50 " href="{{route('invoices.download', ['invoice' => $order->invoice])}}">
                                        Download
                                        Invoice
                                    </flux:link>
                                @endunless
                                <flux:link class="py-1 px-4 rounded-sm hover:bg-neutral-500/50 cursor-pointer" wire:click="deleteOrder({{$order->order_id}})"
                                           wire:confirm="Are you sure to delete order # {{$order->order_id}} ? This action cannot be undone!">
                                    Delete
                                </flux:link>
                            </div>
                        </div>
                    </div>
                    {{--customer and due date info--}}
                    <div class="flex justify-between gap-4 items-center">
                        <span class="text-accent">
                            {{$order->customer->customer_name}}
                        </span>
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
                        <div class="flex-col gap-4 flex" x-cloak x-show="detailsMenuOpen" x-transition>
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
                                    <flux:separator/>
                                @endforeach
                            </div>
                        </div>
                        <flux:button x-on:click="detailsMenuOpen = !detailsMenuOpen">
                            <template x-if="detailsMenuOpen">
                                <flux:icon.chevron-double-up class="text-accent"/>
                            </template>
                            <template x-if="!detailsMenuOpen">
                                <flux:icon.chevron-double-down class="text-accent"/>
                            </template>
                        </flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

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
                class="relative z-10 w-full max-w-md p-6 bg-white dark:bg-zinc-800 rounded-lg shadow-lg dark:text-white text-black flex flex-col gap-4"
                x-data x-on:click.outside="$wire.closeStatusUpdateModal()">
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
                    @foreach(OrderStatus::cases() as $status)
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
