@use(Illuminate\Support\Carbon)
<x-layout>
    <div class="page-section">
        <h2 class="section-title">Standing Order #{{$order->standing_order_id}}</h2>
        <a href="{{route('standing-orders.edit', compact('order'))}}" class="action-link">Edit Order</a>
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
                    <td>{{dayDate($order->start_from)}}</td>
                </tr>
                <tr>
                    <td>Created At</td>
                    <td>{{dayDate(Carbon::parse($order->created_at)->toDateString())}}</td>
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
                                <td>{{$qty}}</td>
                            @endforeach
                        @else
                            {{--If there is no day = no products for that day--}}
                            {{--Show everything as 0 for the day --}}
                            @for($j = 0; $j < $products->count(); $j++)
                                <td>0</td>
                            @endfor
                        @endif
                    </tr>
                @endfor
            </table>
        </div>
    </div>
</x-layout>
