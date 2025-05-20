<x-layout>
    <div class="form-wrapper">
        <h2 class="form-title">Add custom unit prices for {{$customer->customer_name}}</h2>
        <h3>Empty fields will be ignored and treated as default price</h3>
        <h3>Fields left as default price will be ignored</h3>
        <x-error/>
        <form action="{{route('customers.store.custom-price', compact('customer'))}}" method="POST">
            @csrf
            @foreach($products as $product)
                <div class="input-wrapper">
                    <label for="products[{{$product->product_id}}]">{{$product->product_name}} - base: £{{$product->product_unit_price}}</label>
                    <input type="number"
                           id="products[{{$product->product_id}}]"
                           name="products[{{$product->product_id}}]"
                           value="{{$product->price}}"
                           step="0.01"
                           placeholder="Unit price"
                           min="0.05"
                    >
                </div>
            @endforeach
            <input type="submit" value="Submit" class="form-submit-button">
        </form>
    </div>
</x-layout>

