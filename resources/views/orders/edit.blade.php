<x-layout>
    <div class="form-wrapper">
        <h2 class="form-title">Edit order #{{$order->order_id}}</h2>
        <h3>Customer:
            <a class="action-link" href="{{route('customers.show', ['customer' => $order->customer])}}">
                {{$order->customer->customer_name}}
            </a>
        </h3>
        <x-error/>
        <form action="{{route('orders.update', compact('order'))}}" method="POST">
            @csrf
            @method('PUT')
            <div class="input-wrapper">
                <label for="order_placed_at">Order Placed At</label>
                <input type="date" id="order_placed_at" name="order_placed_at"
                       value="{{$order->order_placed_at}}">
            </div>
            <div class="input-wrapper">
                <label for="order_due_at">Order Due At</label>
                <input type="date" id="order_due_at" name="order_due_at" value="{{$order->order_due_at}}">
            </div>
            <div class="input-wrapper">
                <label for="order_status">Order Status</label>
                <select name="order_status" id="order_status">
                    @foreach(\App\Enums\OrderStatus::cases() as $status)
                        <option
                            value="{{$status->name}}"
                            {{$status->name === $order->order_status ? "selected" : ""}}>
                            {{$status->value}}
                        </option>
                    @endforeach
                </select>
            </div>
            <h3>Products</h3>
            @foreach($products as $product)
                @php
                    $orderProduct = $order->products->firstWhere('product_id', $product->product_id);
                @endphp
                <div class="input-wrapper">
                    <label for="products[{{$product->product_id}}]">{{$product->product_name}} -
                        £{{$product->price}}</label>
                    <input type="number"
                           id="products[{{$product->product_id}}]"
                           name="products[{{$product->product_id}}]"
                           value="{{$orderProduct ? $orderProduct->pivot->product_qty : 0}}"
                    >
                </div>
            @endforeach
            <input type="submit" value="Update" class="form-submit-button">
        </form>
    </div>
</x-layout>
