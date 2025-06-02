@use(Illuminate\Support\Carbon)
<x-layout>
    <div class="page-section">
        <h2 class="section-title">Standing Order #{{$order->standing_order_id}}</h2>
        <div class="table-wrapper">
            <table>
                <tr>
                    <td>Customer</td>
                    <td>{{$order->customer->customer_name}}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>{{$order->is_active ? "Active" : "Inactive"}}</td>
                </tr>
                <tr>
                    <td>Starts From</td>
                    <td>{{$order->start_from}}</td>
                </tr>
                <tr>
                    <td>Created At</td>
                    <td>{{Carbon::parse($order->created_at)->toDateString()}}</td>
                </tr>
            </table>
        </div>
        <h2 class="section-title">
            Products
        </h2>
        <div class="table-wrapper">
            <table>
                <tr>
                    <td></td>
                    @foreach($products as $product)
                        <td>{{$product->product_name}}</td>
                    @endforeach
                </tr>
                @for($i = 0;$i<7;$i++)
                    <tr>
                        <td>{{Carbon::create()->startOfWeek()->addDays($i)->dayName}}</td>
                        @php
                            $day = $order->days->where('day', $i)->first();
                            // check if the day exists
                            if($day){
                                $orderProducts = $day->products->sortBy('product_id');
                            }
                        @endphp
                        {{--If there is no day = no products for that day--}}
                        {{--Show everything as 0 for the day --}}
                        @unless($day)
                            @for($j = 0; $j < $products->count(); $j++)
                                <td>0</td>
                            @endfor
                        @endunless
                        @if($day)
                            @foreach($products as $product)
                                @php
                                    $qty = 0;
                                    if($orderProducts->contains(fn($v) => $v->product_id == $product->product_id)){
                                        $qty =  $orderProducts->where('product_id', $product->product_id)->first()->product_qty;
                                    }
                                @endphp
                                <td>{{$qty}}</td>
                            @endforeach
                        @endif
                    </tr>
                @endfor
            </table>
        </div>
    </div>
</x-layout>
