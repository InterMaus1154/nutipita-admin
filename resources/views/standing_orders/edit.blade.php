<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Edit Standing Order #{{$order->standing_order_id}}"/>
        <div class="flex gap-2">
            <flux:badge>Customer:</flux:badge>
            <flux:link
                href="{{route('customers.show', ['customer' => $order->customer])}}">{{$order->customer->customer_name}}</flux:link>
        </div>
        <x-error/>
        <x-success/>
        <form action="{{route('standing-orders.update', compact('order'))}}" method="POST" class="flex flex-col gap-4">
            @csrf
            @method('PUT')
            {{--hidden customer id--}}
            <input class="block text-sm font-medium mb-2 dark:text-white" type="hidden" name="customer_id"
                   value="{{$order->customer->customer_id}}">
            {{--------}}
            <div class="max-w-sm">
                <label class="block text-sm font-medium mb-2 dark:text-white" for="is_active">Is Active?</label>
                <input type="checkbox" name="is_active" @checked($order->is_active) id="is_active" value="1" >
            </div>
            <div class="max-w-sm">
                <label class="block text-sm font-medium mb-2 dark:text-white" for="start_from">Start From</label>
                <input type="date" id="start_from" name="start_from"
                       value="{{$order->start_from}}" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="flex gap-8 flex-wrap my-2">
                {{--count the days from 0 to 7--}}
                @for($i = 0; $i<7;$i++)
                    <div
                        class="flex flex-col gap-4 bg-white border border-gray-200 shadow-2xs rounded-xl p-4 md:p-5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400">
                        <h4 class="font-bold">{{\Illuminate\Support\Carbon::create()->startOfWeek()->addDays($i)->dayName}}</h4>
                        @php
                            $orderHasDay = ($order->days->where('day', $i)->count() > 0);
                            if($orderHasDay){
                                // get products for the current day - if day exists
                                $dayProducts = $order->days->where('day', $i)->first()->products;
                            }
                        @endphp
                        {{--if order has the current day--}}
                        @if($orderHasDay)
                            @foreach($products as $product)
                                @php
                                    $qty = 0;
                                    // if the day contains the current product
                                    if($dayProducts->contains(fn($v) => $v->product_id == $product->product_id)){
                                        $qty =  $dayProducts->where('product_id', $product->product_id)->first()->product_qty;
                                    }
                                @endphp
                                <div class="max-w-sm">
                                    <label
                                        class="block text-sm font-medium mb-2 dark:text-white"
                                        for="products[{{$i}}][{{$product->product_id}}]">{{$product->product_name}}</label>
                                    <input
                                        type="number"
                                        value="{{$qty}}"
                                        id="products[{{$i}}][{{$product->product_id}}]"
                                        name="products[{{$i}}][{{$product->product_id}}]"
                                        class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                    >
                                </div>
                            @endforeach
                        @else
                            {{--if order doesn't have the current day--}}
                            @foreach($products as $product)
                                <div class="input-wrapper">
                                    <label
                                        for="products[{{$i}}][{{$product->product_id}}]"
                                        class="block text-sm font-medium mb-2 dark:text-white">{{$product->product_name}}</label>
                                    <input
                                        type="number"
                                        value="0"
                                        id="products[{{$i}}][{{$product->product_id}}]"
                                        name="products[{{$i}}][{{$product->product_id}}]"
                                        class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"
                                    >
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endfor
            </div>
            <input type="submit" class="form-submit-button" value="Update">
        </form>
    </x-page-section>
</x-flux-layout>
