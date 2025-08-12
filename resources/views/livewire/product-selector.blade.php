<div class="flex flex-col gap-4">
    {{--customer input--}}
    <x-form.customer-select />
    {{--product fields--}}
    <div class="flex flex-col gap-4 items-start">
        @foreach($products as $product)
            {{--show only products where customer has price--}}
            @if($product->price > 0)
                <x-form.form-wrapper>
                    <x-form.form-label id="products-{{$product->product_id}}">
                        {{$product->product_name}} {{$product->product_weight_g}}g - @unitPriceFormat($product->price)
                    </x-form.form-label>
                    <x-form.form-input type="number" id="products-{{$product->product_id}}"
                                       name="products[{{$product->product_id}}]" placeholder="0"/>
                </x-form.form-wrapper>
            @endif
        @endforeach
    </div>
</div>
