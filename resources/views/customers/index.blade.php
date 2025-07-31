<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Customers"/>
        <x-error/>
        <x-success/>
        <flux:link href="{{route('customers.create')}}">Add new customer</flux:link>
        <x-table.table>
            <x-table.head>
                <x-table.header>
                    Name
                </x-table.header>
                <x-table.header>
                    Address
                </x-table.header>
                <x-table.header>
                    Email
                </x-table.header>
                <x-table.header>
                    Phone
                </x-table.header>
                {{--render products for product unit prices/customer--}}
                @foreach($products as $product)
                    <x-table.header>{{$product->product_name}}</x-table.header>
                @endforeach
                <x-table.header>
                    Total Orders
                </x-table.header>
                <x-table.header>
                    Actions
                </x-table.header>
            </x-table.head>
            <x-table.body>
                @foreach($customers as $customer)
                    <x-table.row>
                        <x-table.data>
                            <flux:link
                                href="{{route('customers.show', compact('customer'))}}">{{$customer->customer_name}}</flux:link>
                        </x-table.data>
                        <x-table.data>
                            {{$customer->customer_address_1}}, {{$customer->customer_address_2}}<br>
                            {{$customer->customer_city}}, {{$customer->customer_postcode}}
                        </x-table.data>
                        <x-table.data>
                            @if($customer->customer_email)
                                <flux:link
                                    href="mailto:{{$customer->customer_email}}">{{$customer->customer_email}}</flux:link>
                            @else
                                <em>No email provided!</em>
                            @endif
                        </x-table.data>
                        <x-table.data>
                            @if($customer->customer_phone)
                                <flux:link
                                    href="tel:{{$customer->customer_phone}}">{{$customer->customer_phone}}</flux:link>
                            @else
                                <em>No phone provided!</em>
                            @endif
                        </x-table.data>
                        @foreach($products as $product)
                            @php
                                $product = $product->setCurrentCustomer($customer);
                            @endphp
                            <x-table.data>
                                @if($product->price > 0)
                                    @unitPriceFormat($product->price)
                                @endif
                            </x-table.data>
                        @endforeach
                        <x-table.data>
                            {{$customer->orders_count}}
                        </x-table.data>
                        <x-table.data>
                            <flux:link href="{{route('customers.show', compact('customer'))}}">View
                            </flux:link>
                            <flux:link href="{{route('customers.edit', compact('customer'))}}">Edit
                            </flux:link>
                            <flux:link href="{{route('customers.edit.custom-price', compact('customer'))}}">
                                Edit Prices
                            </flux:link>
                        </x-table.data>
                    </x-table.row>
                @endforeach
            </x-table.body>
        </x-table.table>
    </x-page-section>
</x-flux-layout>
