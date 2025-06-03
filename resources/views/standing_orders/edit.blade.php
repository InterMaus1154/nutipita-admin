<x-layout>
    <div class="form-wrapper standing-order-form">
        <h2 class="form-title">Edit Standing Order #{{$order->standing_order_id}}</h2>
        <x-error/>
        <form action="{{route('standing-orders.update', compact('order'))}}" method="POST">
            @csrf
            @method('PUT')
            <div class="input-wrapper">
                <h3>Customer: {{$order->customer->customer_name}}</h3>
                <input type="hidden" name="customer_id" value="{{$order->customer->customer_id}}">
            </div>
            <div class="input-wrapper">
                <label for="is_active">Is Active?</label>
                <input type="checkbox" name="is_active" @checked($order->is_active) id="is_active" value="{{$order->is_active}}">
            </div>
            <div class="input-wrapper">
                <label for="start_from">Start From</label>
                <input type="date" id="start_from" name="start_from"
                       value="{{old('start_from', now()->toDateString())}}">
            </div>
            <div class="days-wrapper">
                {{--count the days from 0 to 7--}}
                @for($i = 0; $i<7;$i++)
                    <div class="day-wrapper">
                        <h4>{{\Illuminate\Support\Carbon::create()->startOfWeek()->addDays($i)->dayName}}</h4>
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
                                <div class="input-wrapper">
                                    <label
                                        for="products[{{$i}}][{{$product->product_id}}]">{{$product->product_name}}</label>
                                    <input
                                        type="number"
                                        value="{{$qty}}"
                                        id="products[{{$i}}][{{$product->product_id}}]"
                                        name="products[{{$i}}][{{$product->product_id}}]"
                                    >
                                </div>
                            @endforeach
                        @else
                            {{--if order doesn't have the current day--}}
                            @foreach($products as $product)
                                <div class="input-wrapper">
                                    <label
                                        for="products[{{$i}}][{{$product->product_id}}]">{{$product->product_name}}</label>
                                    <input
                                        type="number"
                                        value="0"
                                        id="products[{{$i}}][{{$product->product_id}}]"
                                        name="products[{{$i}}][{{$product->product_id}}]"
                                    >
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endfor
            </div>
            <input type="submit" class="form-submit-button" value="Update">
        </form>
    </div>
</x-layout>
