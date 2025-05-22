<div class="customer-custom-prices">
    @if($hasCustomPrices)
        <a class="action-link" href="{{route('customers.edit.custom-price', compact('customer'))}}">Edit custom
            prices
        </a>
    @endif
    @if(!$hasCustomPrices)
        <p>This customer doesn't have custom prices! Base price applies.</p>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Product Base Unit Price</th>
                    <th>Customer Unit Price</th>
                    <th>Modified</th>
                </tr>
                </thead>
                <tbody>
                @foreach($customer->customPrices as $customPrice)
                    <tr>
                        <td>{{$customPrice->product->product_name}}</td>
                        <td>£{{$customPrice->product->product_unit_price}}</td>
                        <td>£{{$customPrice->customer_product_price}}</td>
                        <td>{{\Illuminate\Support\Carbon::parse($customPrice->created_at)->toDateString()}}</td>
                        <td>
                            <button
                                wire:click="delete('{{\Illuminate\Support\Facades\Crypt::encrypt( $customPrice->customer_product_price_id)}}')"
                                wire:confirm="Are you sure you want to delete this custom price?"
                                class="action-link">Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
