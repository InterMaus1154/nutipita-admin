@use(Illuminate\Support\Carbon)
@use(Illuminate\Support\Facades\Request)
<div class="table-wrapper">
    <table>
        <thead>
        <tr>
            <th>
                ID #
            </th>
            <th>
                Customer
            </th>
            <th>
                Placed Date
            </th>
            <th>
                Due Date
            </th>
            <th>
                Status
            </th>
            @foreach($products as $product)
                <th>{{$product->product_name}}</th>
            @endforeach
            <th>
                Total Pita
            </th>
            <th>
                Total £
            </th>
        </tr>
        </thead>
        <tbody>
        @forelse($orders as $order)
            <tr @class([
                    'green-row-bg' => $order->is_standing
                ])>
                <td>
                    <a class="action-link" href="{{route('orders.show', compact('order'))}}">
                        #{{$order->order_id}}
                    </a>
                </td>
                <td>
                    <a href="{{route('customers.show', ['customer' => $order->customer])}}" class="action-link">
                        {{$order->customer->customer_name}}
                    </a>
                </td>
                <td>
                    {{dayDate($order->order_placed_at)}}
                </td>
                <td>
                    {{dayDate($order->order_due_at)}}
                </td>
                <td>
                    {{$order->status}}
                </td>
                @foreach($products as $product)
                    @php
                        $orderProduct = $order->products->firstWhere('product_id', $product->product_id);
                    @endphp
                    <td>
                        @if($orderProduct)
                            {{$orderProduct->pivot->product_qty}} x
                            £{{$orderProduct->pivot->order_product_unit_price}}
                            <br>
                            £{{$orderProduct->pivot->product_qty * $orderProduct->pivot->order_product_unit_price}}
                        @else
                            0
                        @endif
                    </td>
                @endforeach
                <td>
                    {{$order->total_pita}}
                </td>
                <td>
                    {{$order->total_price_format}}
                </td>
                <td>
                    <a href="{{route('orders.edit', compact('order'))}}" class="action-link table" style="margin-bottom: 8px">
                        Edit
                    </a>
                    @if(isset($onOrderIndex) && $onOrderIndex)
                        <a wire:confirm="Are you sure you want to delete this order?"
                           wire:click="delete({{$order->order_id}})"
                           class="action-link table"
                           style="margin-bottom: 8px">
                            Delete
                        </a>
                    @endif
                    <a href="{{route('invoices.create-single', compact('order'))}}" class="action-link table">Generate Invoice</a>
                </td>
            </tr>
        @empty
            <tr style="text-align: center">
                <td>No orders found!</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
