<x-layout>
    <section class="page-section">
        <h2 class="section-title">Product Details</h2>
        <x-success/>
        <h3>{{$product->product_name}}</h3>
        <a href="{{route('products.edit', compact('product'))}}" class="action-link">Update product</a>
        <div class="table-wrapper">
            <table>
                <tbody>
                <tr>
                    <td>
                        Unit price:
                    </td>
                    <td>£{{$product->product_unit_price}}</td>
                </tr>
                <tr>
                    <td>Pack Price:</td>
                    <td>
                        @if($product->product_qty_per_pack)
                            £{{$product->product_qty_per_pack * $product->product_unit_price}}
                        @else
                            <em>No pack price without qty</em>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Product Weight (g);</td>
                    <td>@if($product->product_weight_g)
                            {{$product->product_weight_g}}g
                        @else
                            <em>No specified weight</em>
                        @endif</td>
                </tr>
                <tr>
                    <td>Pieces / pack</td>
                    <td>
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
    </section>
</x-layout>
