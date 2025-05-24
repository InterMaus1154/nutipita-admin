<x-layout>
    <div class="form-wrapper">
        <h2 class="form-title">Add new product</h2>
        <x-error />
        <form action="{{route('products.store')}}" method="POST">
            @csrf
            {{--Product name--}}
            <div class="input-wrapper">
                <label for="product_name">Product Name</label>
                <input type="text" id="product_name" name="product_name" value="{{old('product_name', '')}}" />
            </div>
            {{--Product weight--}}
            <div class="input-wrapper">
                <label for="product_weight_g">Product unit weight (gramm) (optional)</label>
                <input type="number" id="product_weight_g" name="product_weight_g" value="{{old('product_weight_g', '')}}" />
            </div>
            {{--Product qty per pack--}}
            <div class="input-wrapper">
                <label for="product_qty_per_pack">Product quantity per pack (optional)</label>
                <input type="number" id="product_qty_per_pack" name="product_qty_per_pack" value="{{old('product_qty_per_pack', '')}}" />
            </div>
            <input type="submit" value="Add" class="form-submit-button">
        </form>
    </div>
</x-layout>
<?php
