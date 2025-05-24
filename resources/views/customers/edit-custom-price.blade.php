<x-layout>
    <div class="form-wrapper">
        <h2 class="form-title">Prices for {{$customer->customer_name}}</h2>
        <x-error/>
        <form action="{{route('customers.update.custom-price', compact('customer'))}}" method="POST">
            @csrf
            @method('PUT')
            @foreach($products as $product)
                <div class="input-wrapper">
                    <label for="products[{{$product->product_id}}]">{{$product->product_name}}</label>
                    <input type="number"
                           id="products[{{$product->product_id}}]"
                           name="products[{{$product->product_id}}]"
                           value="{{$product->price}}"
                           step="0.01"
                           placeholder="Unit price"
                    >
                </div>
            @endforeach
            <input type="submit" value="Submit" class="form-submit-button">
        </form>
    </div>
</x-layout>

