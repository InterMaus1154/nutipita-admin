<div class="flex flex-col gap-4">
    {{--customer input--}}
    <x-form.customer-select has-wire="true"/>
    {{--product fields--}}
    <div class="flex flex-col gap-4 items-start">
        @if(isset($customer_id) && $products->isEmpty())
            <p class="text-red-500">This customer has no prices set for any product!</p>
        @endif
        @foreach($products as $product)
            <x-form.form-wrapper>
                <x-form.form-label id="products-{{$product->product_id}}">
                    {{$product->product_name}} {{$product->product_weight_g}}g - @unitPriceFormat($product->price)
                </x-form.form-label>
                <x-form.form-input type="number" id="products-{{$product->product_id}}"
                                   name="products[{{$product->product_id}}]" placeholder="0" value="{{old('products.'.$product->product_id)}}"/>
            </x-form.form-wrapper>
        @endforeach
    </div>
</div>
