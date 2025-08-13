<div class="flex flex-col">
    <x-success/>
    <x-error/>
    <x-table.table smallTable>
        <x-table.head>
            <x-table.header sortField="product_name">
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
                <x-table.row wire:key="product-{{$product->product_id}}">
                    <x-table.data>
                            <span class="text-accent">
                                {{$product->product_name}}
                            </span>
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
</div>
