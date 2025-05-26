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
            <div class="input-wrapper">
                <label for="order_placed_at">Order Placed At</label>
                <input type="date" id="order_placed_at" name="order_placed_at"
                       value="{{old('order_placed_at', now()->toDateString())}}">
            </div>
            <div class="input-wrapper">
                <label for="order_due_at">Order Due At (optional)</label>
                <input type="date" id="order_due_at" name="order_due_at" value="{{old('order_due_at', '')}}">
            </div>
            <input type="submit" value="Update" class="form-submit-button">
        </form>
    </div>
</x-layout>
