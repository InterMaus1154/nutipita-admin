<x-layout>
    <section class="page-section">
        <h2 class="section-title">Products</h2>
        <x-success/>
        <x-error/>
        <a href="{{route('products.create')}}" class="action-link">Add new product</a>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>
                        ID
                    </th>
                    <th>
                        Name
                    </th>
                    <th>
                        Unit Price
                    </th>
                    <th>
                        Pack Price
                    </th>
                    <th>
                        Unit Weight
                    </th>
                    <th>
                        Qty / pack
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <a class="action-link" href="{{route('products.show', compact('product'))}}">
                                {{$product->product_id}}
                            </a>
                        </td>
                        <td>{{$product->product_name}}</td>
                        <td>£{{$product->product_unit_price}}</td>
                        <td>
                            @if($product->product_qty_per_pack)
                                £{{$product->product_unit_price * $product->product_qty_per_pack}}
                            @else
                                <em>No pack price without qty</em>
                            @endif
                        </td>
                        <td>
                            @if($product->product_weight_g)
                                {{$product->product_weight_g}}g
                            @else
                                <em>No specified weight</em>
                            @endif
                        </td>
                        <td>
                            @if($product->product_qty_per_pack)
                                {{$product->product_qty_per_pack}}pcs
                            @else
                                <em>No specified pack quantity</em>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr style="text-align: center">
                        <td>No products found!</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layout>
