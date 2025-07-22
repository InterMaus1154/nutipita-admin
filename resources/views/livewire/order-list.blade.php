@use(Illuminate\Support\Carbon)
@use(Illuminate\Support\Facades\Request)
@use(Illuminate\Database\Eloquent\Collection as EloquentCollection)
@props(['withSummaries' => false, 'withIncome' => false, 'products'])
<div
    class="space-y-4">
    @if(isset($onOrderIndex) && $onOrderIndex)
        <flux:button id="showSummaryBtn">Show Summaries</flux:button>
    @endif
    <div class="hidden" id="summaryContainer">
        {{--condition to render summary boxes or not--}}
        {{--render only if set to true, and there is a full eloquent collection orders, not paginated version--}}
        @if($withSummaries && (isset($ordersAll) || $orders instanceof EloquentCollection))
            @php
                $summaryOrders = $ordersAll ?? $orders;
                $totalIncome = 0;
                $totalPita = 0;
                $productTotals = [];
                foreach ($summaryOrders as $order) {

                    // calculate total income for all orders
                    $totalIncome += $order->total_price;

                    $totalPita += $order->total_pita;

                    // calculate product quantity total for each product
                    foreach ($products as $product) {
                        if(isset($productTotals[$product->product_name])){
                            $productTotals[$product->product_name] += $order->getTotalOfProduct($product);
                        }else{
                            $productTotals[$product->product_name] = $order->getTotalOfProduct($product);
                        }
                    }
                }
            @endphp
            <div class="flex gap-6 flex-wrap">
                <x-data-box dataBoxHeader="Total Orders" :dataBoxValue="number_format($ordersAll->count())"/>
                <x-data-box dataBoxHeader="Total Pita" :dataBoxValue="number_format($totalPita)"/>
                @if($withIncome)
                    <x-data-box dataBoxHeader="Total Income" :dataBoxValue="formatMoneyCurrency($totalIncome)"/>
                @endif
                @foreach($productTotals as $productName => $productQty)
                    @unless(empty($productQty))
                        <x-data-box :dataBoxHeader="$productName" :dataBoxValue="number_format($productQty)"/>
                    @endunless
                @endforeach
            </div>
        @endif
    </div>
    @if($orders instanceof \Illuminate\Pagination\Paginator || $orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div>
            {{$orders->links()}}
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
                                {{number_format($orderProduct->pivot->product_qty)}} x
                                @formatMoneyPound($orderProduct->pivot->order_product_unit_price)
                                <br>
                                @formatMoneyPound($orderProduct->pivot->product_qty * $orderProduct->pivot->order_product_unit_price)
                            @else
                                0
                            @endif
                        </x-table.data>
                    @endforeach
                    <x-table.data>
                        {{number_format($order->total_pita)}}
                    </x-table.data>
                    <x-table.data>
                        @formatMoneyPound($order->total_price)
                    </x-table.data>
                    <x-table.data>
                        <flux:link href="{{route('orders.show', compact('order'))}}">View</flux:link>
                        <flux:link href="{{route('orders.edit', compact('order'))}}">Edit</flux:link>
                        {{--                                    @if(isset($onOrderIndex) && $onOrderIndex)--}}
                        {{--                                        <flux:link wire:confirm="Are you sure you want to delete this order?"--}}
                        {{--                                            wire:click="delete({{$order->order_id}})"--}}
                        {{--                                        >Delete</flux:link>--}}
                        {{--                                    @endif--}}
                        <flux:link href="{{route('invoices.create-single', compact('order'))}}">Generate
                            Invoice
                        </flux:link>
                    </x-table.data>
                </x-table.row>
            @empty
                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                    <td class="italic text-lg">No orders found!</td>
                </tr>
            @endforelse
        </x-table.body>
    </x-table.table>
    @if($orders instanceof \Illuminate\Pagination\Paginator || $orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div>
            {{$orders->links()}}
        </div>
    @endif
    <script>
        const showSummaryBtn = document.querySelector("#showSummaryBtn");
        const summaryContainer = document.querySelector("#summaryContainer");

        showSummaryBtn.addEventListener("click", () => {
            summaryContainer.classList.toggle("hidden");
            if (summaryContainer.classList.contains('hidden')) {
                showSummaryBtn.innerText = "Show Summaries";
            }else{
                showSummaryBtn.innerText = "Hide Summaries";
            }
        });
    </script>
</div>
