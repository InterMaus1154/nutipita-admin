<x-layout>
    <div class="form-wrapper">
        <h2 class="form-title">Update product</h2>
        <x-error />
        <form action="{{route('products.update', compact('product'))}}" method="POST">
            @csrf
            @method('PUT')
            {{--Product name--}}
            <div class="input-wrapper">
                <label for="product_name">Product Name</label>
                <input type="text" id="product_name" name="product_name" value="{{$product->product_name}}" />
            </div>
            {{--Product unit price--}}
            <div class="input-wrapper">
                <label for="product_unit_price">Product unit price</label>
                <input type="number" id="product_unit_price" name="product_unit_price" value="{{$product->product_unit_price}}" step=".01"/>
            </div>
            {{--Product weight--}}
            <div class="input-wrapper">
                <label for="product_weight_g">Product unit weight (gramm) (optional)</label>
                <input type="number" id="product_weight_g" name="product_weight_g" value="{{$product->product_weight_g}}" />
            </div>
            {{--Product qty per pack--}}
            <div class="input-wrapper">
                <label for="product_qty_per_pack">Product quantity per pack (optional)</label>
                <input type="number" id="product_qty_per_pack" name="product_qty_per_pack" value="{{$product->product_qty_per_pack}}" />
            </div>
            <input type="submit" value="Update" class="form-submit-button">
        </form>
    </div>
</x-layout>
<?php
