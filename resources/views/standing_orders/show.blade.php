@use(Illuminate\Support\Carbon)
<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Standing Order">
            <flux:link href="{{route('standing-orders.edit', compact('order'))}}">
                <flux:icon.pencil-square class="size-8"/>
            </flux:link>
        </x-page-heading>
        <div class="flex gap-2 justify-center">
            <flux:badge class="px-4 py-2 !text-lg text-accent!">
                {{$order->customer->customer_name}}
            </flux:badge>
        </div>
        <div class="flex flex-col mx-auto my-12">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 inline-block align-middle">
                    <div class="overflow-hidden">
                        <table
                                class="divide-y divide-gray-200 dark:divide-neutral-700 border border-zinc-600 border-solid">
                            <x-table.row>
                                <x-table.data>
                                    Status
                                </x-table.data>
                                <x-table.data>
                                    @if($order->is_active)
                                        <flux:badge color="green">Active</flux:badge>
                                    @else
                                        <flux:badge color="rose">Inactive</flux:badge>
                                    @endif
                                </x-table.data>
                            </x-table.row>
                            <x-table.row>
                                <x-table.data>
                                    Created At
                                </x-table.data>
                                <x-table.data>{{dayDate(Carbon::parse($order->created_at)->toDateString())}}</x-table.data>
                            </x-table.row>
                            <x-table.row>
                                <x-table.data>
                                    Starts From
                                </x-table.data>
                                <x-table.data>{{dayDate($order->start_from)}}</x-table.data>
                            </x-table.row>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <h3 class="font-bold text-center text-xl text-accent">
            Products
        </h3>
        <div class="flex flex-col mx-auto md:w-[75%]">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700 border-1 border-neutral-700">
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center border-1 border-neutral-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"></td>
                                @foreach($products as $product)
                                    <th class="px-2 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">{{$product->product_name}} {{$product->product_weight_g}}
                                        g
                                    </th>
                                @endforeach
                            </tr>
                            @for($i = 0;$i<7;$i++)
                                <tr class="even:bg-white odd:bg-gray-100 hover:bg-gray-100 dark:even:bg-neutral-800 dark:odd:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <th class="px-2 py-3 text-center text-md font-medium text-white dark:text-white">{{Carbon::create()->startOfWeek()->addDays($i)->dayName}}</th>
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
                                            <td @class([
                                                'px-6 py-4 whitespace-nowrap text-sm font-medium',
                                                'text-white dark:text-gray-500' => $qty === 0
                                                ])>@amountFormat($qty)
                                            </td>
                                        @endforeach
                                    @else
                                        {{--If there is no day = no products for that day--}}
                                        {{--Show everything as 0 for the day --}}
                                        @for($j = 0; $j < $products->count(); $j++)
                                            <td @class([
                                                'px-6 py-4 whitespace-nowrap text-sm font-medium',
                                                'text-white dark:text-gray-500' => $qty === 0
                                                ])>
                                                0
                                            </td>
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
