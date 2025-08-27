@props(['product'])
<x-ui.mobile-card-skeleton>
    @php
        /**
* @var \App\Models\Product $product
 */
    @endphp
    {{--card header--}}
    <div class="grid grid-cols-[1fr_auto_1fr] gap-2">
        <div></div>
        <div class="justify-self-center text-center">
            <span class="text-lg text-center font-bold text-accent">
            {{$product->product_name}}
            </span>
        </div>
        <x-ui.mobile-card-dropdown-menu class="justify-self-end">
            <x-ui.mobile-card-dropdown-link href="{{route('products.edit', compact('product'))}}">Edit
            </x-ui.mobile-card-dropdown-link>
        </x-ui.mobile-card-dropdown-menu>
    </div>
    {{--card body--}}
    <div class="flex justify-between gap-2 flex-wrap">
        <div class="flex gap-2 items-center">
            <flux:icon.scale class="text-accent size-5"/>
            <span>{{$product->product_weight_g}}g</span>
        </div>
        <div class="flex gap-2 items-center">
            <flux:icon.package class="text-accent size-5"/>
            <span>{{$product->product_qty_per_pack}}pcs</span>
        </div>
    </div>
</x-ui.mobile-card-skeleton>
