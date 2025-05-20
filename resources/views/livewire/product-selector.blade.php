<div>
    <div class="input-wrapper">
        <label for="customer_id">Customer</label>
        <select id="customer_id" name="customer_id" wire:model.live="customer_id">
            <option value="">---Select a customer---</option>
            @foreach($customers as $customer)
                <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
            @endforeach
        </select>
    </div>
    <h3>Products & quantities</h3>
    <div class="product-list-form">
        @foreach($products as $product)
            <div class="input-wrapper">
                <label for="product-{{$product->product_id}}">{{$product->product_name}} - £{{$product->price}}</label>
                <input type="number" name="products[{{$product->product_id}}]" value="0" placeholder="Quantity">
            </div>
        @endforeach
    </div>
    Selected customer: {{$selectedCustomer?->customer_name}}
</div>
