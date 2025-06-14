<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Customers"/>
        <x-error />
        <x-success/>
        <a href="{{route('customers.create')}}" class="action-link">Add new customer</a>
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
                        Address
                    </th>
                    <th>
                        Email
                    </th>
                    <th>
                        Phone
                    </th>
                    @foreach($products as $product)
                        <th>{{$product->product_name}}</th>
                    @endforeach
                    <th>
                        Total Orders
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>
                            <a class="action-link" href="{{route('customers.show', compact('customer'))}}">
                                {{$customer->customer_id}}
                            </a>
                        </td>
                        <td>
                            <a href="{{route('customers.show', compact('customer'))}}" class="action-link">
                                {{$customer->customer_name}}
                            </a>
                        </td>
                        <td>
                            {{$customer->short_address}}
                        </td>
                        <td>
                            @if($customer->customer_email)
                                <a class="action-link"
                                   href="mailto:{{$customer->customer_email}}">{{$customer->customer_email}}</a>
                            @else
                                <em>No email provided</em>
                            @endif
                        </td>
                        <td>
                            @if($customer->customer_phone)
                                <a class="action-link"
                                   href="tel:{{$customer->customer_phone}}">{{$customer->customer_phone}}</a>
                            @else
                                <em>No phone provided</em>
                            @endif
                        </td>
                        @foreach($products as $product)
                            @php
                                $product = $product->setCurrentCustomer($customer);
                            @endphp
                            <td>
                                {{$product->price === 0 ? "" : "£".$product->price}}
                            </td>
                        @endforeach
                        <td>
                            {{$customer->orders_count}}</td>
                        <td>
                            <a class="action-link table" href="{{route('customers.edit', compact('customer'))}}">Edit
                                Details</a>
                            <a class="action-link table" href="{{route('customers.edit.custom-price', compact('customer'))}}">Edit
                                prices</a>
                        </td>
                    </tr>
                @empty
                    <tr style="text-align: center">
                        <td>No customers found!</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </x-page-section>
</x-flux-layout>
