<x-flux-layout>
    <x-page-section>
        <x-page-heading title="'{{$product->product_name}}' details"/>
        <x-success/>
        <x-error />
        <flux:link href="{{route('products.edit', compact('product'))}}">Update product</flux:link>
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 inline-block align-middle">
                    <div class="overflow-hidden">
                            <table class="divide-y divide-gray-200 dark:divide-neutral-700 border border-zinc-600 border-solid">
                                <tbody>
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">Product Weight (g);</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">@if($product->product_weight_g)
                                            {{$product->product_weight_g}}g
                                        @else
                                            <em>No specified weight</em>
                                        @endif</td>
                                </tr>
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">Pieces / pack</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        @if($product->product_qty_per_pack)
                                            {{$product->product_qty_per_pack}}pcs
                                        @else
                                            <em>No specified pack quantity</em>
                                        @endif
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </x-page-section>
</x-flux-layout>
