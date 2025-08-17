<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Add New Product" />
        <x-error />
        <x-success />
        <form action="{{route('products.store')}}" method="POST" class="flex flex-col gap-4">
            @csrf
            {{--Product name--}}
            <x-form.form-wrapper>
                <x-form.form-label id="product_name" text="Name"/>
                <x-form.form-input id="product_name" name="product_name" value="{{old('product_name', '')}}" placeholder="Name"/>
            </x-form.form-wrapper>
            {{--Product weight--}}
            <x-form.form-wrapper>
                <x-form.form-label id="product_weight_g" text="Weight"/>
                <x-form.form-input type="number" id="product_weight_g" name="product_weight_g" value="{{old('product_weight_g', '')}}" placeholder="Weight (g)"/>
            </x-form.form-wrapper>
            {{--Product qty per pack--}}
            <x-form.form-wrapper>
                <x-form.form-label id="product_qty_per_pack" text="Product/Pack"/>
                <x-form.form-input id="product_qty_per_pack" name="product_qty_per_pack" value="{{old('product_qty_per_pack', '')}}" placeholder="Product/pack"/>
            </x-form.form-wrapper>
            <flux:button variant="primary" type="submit" class="">Add</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
<?php
