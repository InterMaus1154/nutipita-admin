@use(Illuminate\Support\Carbon)
<x-flux-layout>
    <x-page-section class="page-section">
        <x-page-heading title="Standing Order #{{$order->standing_order_id}}"/>
        <flux:link href="{{route('standing-orders.edit', compact('order'))}}">Edit Order</flux:link>
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="divide-y divide-gray-200 dark:divide-neutral-700 border border-zinc-600 border-solid">
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">Customer</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    <flux:link href="{{route('customers.show', ['customer' => $order->customer])}}">{{$order->customer->customer_name}}</flux:link>
                                </td>
                            </tr>
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">Status</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    @if($order->is_active)
                                        <flux:badge color="green">Active</flux:badge>
                                    @else
                                        <flux:badge color="rose">Inactive</flux:badge>
                                    @endif
                                </td>
                            </tr>
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">Starts From</td>
                                <td>{{dayDate($order->start_from)}}</td>
                            </tr>
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">Created At</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{dayDate(Carbon::parse($order->created_at)->toDateString())}}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <h3 class="font-bold">
            Products
        </h3>
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"></td>
                                @foreach($products as $product)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{$product->product_name}}</td>
                                @endforeach
                            </tr>
                            @for($i = 0;$i<7;$i++)
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{Carbon::create()->startOfWeek()->addDays($i)->dayName}}</td>
                                    @php
                                        $day = $order->days->where('day', $i)->first();
                                        // check if the day exists
                                        if($day){
                                            $dayProducts = $day->products->sortBy('product_id');
                                        }
                                    @endphp
                                    @if($day)
                                        @foreach($products as $product)
                                            @php
                                                $qty = 0;
                                                // if the day contains the current product
                                                if($dayProducts->contains(fn($v) => $v->product_id == $product->product_id)){
                                                    $qty =  $dayProducts->where('product_id', $product->product_id)->first()->product_qty;
                                                }
                                            @endphp
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{$qty}}</td>
                                        @endforeach
                                    @else
                                        {{--If there is no day = no products for that day--}}
                                        {{--Show everything as 0 for the day --}}
                                        @for($j = 0; $j < $products->count(); $j++)
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">0</td>
                                        @endfor
                                    @endif
                                </tr>
                            @endfor
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </x-page-section>
</x-flux-layout>
