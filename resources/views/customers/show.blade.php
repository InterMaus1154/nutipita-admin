<x-layout>
    <section class="page-section">
        <h2 class="section-title">Customer Details</h2>
        <x-success/>
        <x-error/>
        <h3>{{$customer->customer_name}}</h3>
        <a href="{{route('customers.edit', compact('customer'))}}" class="action-link">Update customer</a>
        <div class="table-wrapper">
            <table>
                <tbody>
                <tr>
                    <td>Address</td>
                    <td>
                        @if($customer->customer_address)
                            {{$customer->customer_address}}
                        @else
                            <em>No address provided</em>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>
                        @if($customer->customer_email)
                            <a class="action-link"
                               href="mailto:{{$customer->customer_email}}">{{$customer->customer_email}}</a>
                        @else
                            <em>No email provided</em>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td>
                        @if($customer->customer_phone)
                            <a class="action-link"
                               href="tel:{{$customer->customer_phone}}">{{$customer->customer_phone}}</a>
                        @else
                            <em>No phone provided</em>
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        {{--custom prices section--}}
        <h2 class="section-title">Custom prices</h2>
        <a href="{{route('customers.create.custom-price', compact('customer'))}}" class="action-link">Add custom
            prices
        </a>
        @if($hasCustomPrices)
            <a class="action-link" href="{{route('customers.edit.custom-price', compact('customer'))}}">Edit custom prices</a>
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
                                <form method="POST" action="{{route('customers.delete.custom-price', compact('customer', 'customPrice'))}}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="submit" value="Delete" class="action-link">
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        {{--custom orders section--}}
        <h2 class="section-title">Customer Orders</h2>
    </section>
</x-layout>
