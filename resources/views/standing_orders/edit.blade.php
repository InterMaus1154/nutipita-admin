<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Edit Standing Order"/>
        <div class="flex gap-2 justify-center">
            <flux:badge class="px-4 py-2 !text-lg text-accent!">
                {{$order->customer->customer_name}}
            </flux:badge>
        </div>
        <x-error/>
        <x-success/>
        <form action="{{route('standing-orders.update', compact('order'))}}" method="POST" class="flex flex-col gap-4 items-center">
            @csrf
            @method('PUT')
            {{--hidden customer id--}}
            <input class="block text-sm font-medium mb-2 dark:text-white" type="hidden" name="customer_id"
                   value="{{$order->customer->customer_id}}">
            {{--------}}
{{--            <div class="max-w-sm">--}}
{{--                <label class="block text-sm font-medium mb-2 dark:text-white" for="is_active">Is Active?</label>--}}
{{--                <input type="checkbox" name="is_active" @checked($order->is_active) id="is_active" value="1">--}}
{{--            </div>--}}
            <x-form.form-wrapper>
                <x-form.form-label id="start_from" text="Start From"/>
                <x-form.form-input type="date" id="start_from" name="start_from" value="{{$order->start_from}}"/>
            </x-form.form-wrapper>
            <div class="flex gap-8 flex-wrap my-2 justify-center">
                {{--count the days from 0 to 7--}}
                @for($i = 0; $i<7;$i++)
                    <div
                        class="flex flex-col gap-4 bg-white border border-gray-200 shadow-2xs rounded-xl p-4 md:p-5 dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400">
                        <h4 class="font-bold text-center text-accent">{{\Illuminate\Support\Carbon::create()->startOfWeek()->addDays($i)->dayName}}</h4>
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
                                    $qty = null;
                                    // if the day contains the current product
                                    if($dayProducts->contains(fn($v) => $v->product_id == $product->product_id)){
                                        $qty = $dayProducts->where('product_id', $product->product_id)->first()->product_qty;
                                    }
                                @endphp
                                <x-form.form-wrapper>
                                    <x-form.form-label id="products[{{$i}}][{{$product->product_id}}]">
                                        {{$product->product_name}} {{$product->product_weight_g}}g
                                    </x-form.form-label>
                                    <x-form.form-input type="number" id="products[{{$i}}][{{$product->product_id}}]" name="products[{{$i}}][{{$product->product_id}}]" value="{{$qty}}" placeholder="0"/>
                                </x-form.form-wrapper>
                            @endforeach
                        @else
                            {{--if order doesn't have the current day--}}
                            @foreach($products as $product)
                                <x-form.form-wrapper>
                                    <x-form.form-label id="products[{{$i}}][{{$product->product_id}}]">
                                        {{$product->product_name}} {{$product->product_weight_g}}g
                                    </x-form.form-label>
                                    <x-form.form-input type="number" id="products[{{$i}}][{{$product->product_id}}]" name="products[{{$i}}][{{$product->product_id}}]" placeholder="0"/>
                                </x-form.form-wrapper>
                            @endforeach
                        @endif
                    </div>
                @endfor
            </div>
            <flux:button variant="primary" type="submit" class="sm:min-w-[41%] mx-auto">Update</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
