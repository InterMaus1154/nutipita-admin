<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Products">
            <flux:link href="{{route('products.create')}}">
                <flux:icon.plus-circle class="size-8"/>
            </flux:link>
        </x-page-heading>
        <x-success/>
        <x-error/>
        <x-table.table>
            <x-table.head>
                <x-table.header>
                    Name
                </x-table.header>
                <x-table.header>
                    Unit Weight(g)
                </x-table.header>
                <x-table.header>
                    Qty / Pack
                </x-table.header>
                <x-table.header>
                    Actions
                </x-table.header>
            </x-table.head>
            <x-table.body>
                @foreach($products as $product)
                    <x-table.row>
                        <x-table.data>
                            <flux:link
                                href="{{route('products.show', compact('product'))}}">{{$product->product_name}}</flux:link>
                        </x-table.data>
                        <x-table.data>
                            @if($product->product_weight_g)
                                {{$product->product_weight_g}}g
                            @else
                                -
                            @endif
                        </x-table.data>
                        <x-table.data>
                            @if($product->product_qty_per_pack)
                                {{$product->product_qty_per_pack}}pcs
                            @else
                                -
                            @endif
                        </x-table.data>
                        <x-table.data>
                            <flux:link
                                href="{{route('products.edit', compact('product'))}}">
                                <flux:icon.pencil-square class="!inline"/>
                            </flux:link>
                        </x-table.data>
                    </x-table.row>
                @endforeach
            </x-table.body>
        </x-table.table>
    </x-page-section>
</x-flux-layout>
