<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Edit '{{$product->product_name}}'"/>
        <x-error/>
        <x-success/>
        <form action="{{route('products.update', compact('product'))}}" method="POST" class="flex flex-col gap-4">
            @csrf
            @method('PUT')
            {{--Product name--}}
            <div class="max-w-sm">
                <label for="product_name" class="block text-sm font-medium mb-2 dark:text-white">Product Name</label>
                <input type="text" id="product_name" name="product_name" value="{{$product->product_name}}"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"/>
            </div>
            {{--Product weight--}}
            <div class="max-w-sm">
                <label for="product_weight_g" class="block text-sm font-medium mb-2 dark:text-white">Product unit weight
                    (gramm) (optional)</label>
                <input type="number" id="product_weight_g" name="product_weight_g"
                       value="{{$product->product_weight_g}}"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"/>
            </div>
            {{--Product qty per pack--}}
            <div class="max-w-sm">
                <label for="product_qty_per_pack" class="block text-sm font-medium mb-2 dark:text-white">Product
                    quantity per pack (optional)</label>
                <input type="number" id="product_qty_per_pack" name="product_qty_per_pack"
                       value="{{$product->product_qty_per_pack}}"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600"/>
            </div>
            <flux:button variant="primary" type="submit">Update</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
<?php
