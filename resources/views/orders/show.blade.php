<x-layout>
    <section class="page-section">
        <h2 class="section-title">Order #{{$order->order_id}}</h2>
        <a class="action-link" href="{{route('orders.edit', compact('order'))}}">Edit Order</a>
        <div class="table-wrapper">
            <table>
                <tbody>
                <tr>
                    <td>Customer</td>
                    <td>
                        <a href="{{route('customers.show', ['customer' => $order->customer])}}" class="action-link">
                            {{$order->customer->customer_name}}
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>Placed At</td>
                    <td>{{$order->order_placed_at}}</td>
                </tr>
                <tr>
                    <td>Due At</td>
                    <td>{{$order->order_due_at ?? "No specified due date"}}</td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>{{$order->status}}</td>
                </tr>
                <tr>
                    <td>Total pita</td>
                    <td>{{$order->total_pita}}</td>
                </tr>
                <tr>
                    <td>Total price</td>
                    <td>£{{$order->total_price}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <h2 class="section-title">Order Products</h2>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>
                        ID
                    </th>
                    <th>
                        Name
                    </th>
                    <th>
                        Order Unit Price
                    </th>
                    <th>
                        Pack Price
                    </th>
                    <th>
                        Unit Weight
                    </th>
                    <th>
                        Qty / pack
                    </th>
                    <th>Order Quantity</th>
                    <th>Total Price</th>
                </tr>
                </thead>
                <tbody>
                @foreach($order->products as $product)
                    <tr>
                        <td>
                            <a class="action-link" href="{{route('products.show', compact('product'))}}">
                                {{$product->product_id}}
                            </a>
                        </td>
                        <td>{{$product->product_name}}</td>
                        <td>£{{$product->pivot->order_product_unit_price}}</td>
                        <td>
                            @if($product->product_qty_per_pack)
                                £{{$product->pivot->order_product_unit_price * $product->product_qty_per_pack}}
                            @else
                                <em>No pack price without qty</em>
                            @endif
                        </td>
                        <td>
                            @if($product->product_weight_g)
                                {{$product->product_weight_g}}g
                            @else
                                <em>No specified weight</em>
                            @endif
                        </td>
                        <td>
                            @if($product->product_qty_per_pack)
                                {{$product->product_qty_per_pack}}pcs
                            @else
                                <em>No specified pack quantity</em>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
</x-layout>
