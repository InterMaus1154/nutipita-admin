<x-layout>
    <div class="form-wrapper">
        <h2 class="form-title">Add new order</h2>
        <x-error/>
        <form action="{{route('orders.store')}}" method="POST">
            @csrf
            <div class="input-wrapper">
                <label for="order_placed_at">Order Placed At</label>
                <input type="date" id="order_placed_at" name="order_placed_at" value="{{now()->toDateString()}}">
            </div>
            <div class="input-wrapper">
                <label for="order_due_at">Order Due At (optional)</label>
                <input type="date" id="order_due_at" name="order_due_at" value="{{now()->toDateString()}}">
            </div>
            @livewire('product-selector')
            <input type="submit" value="Add" class="form-submit-button">
        </form>
    </div>
</x-layout>
