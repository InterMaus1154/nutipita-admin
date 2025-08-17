<div class="flex flex-col">
    <x-error/>
    <x-success/>
    <x-table.table class="md:max-w-[75%] mx-auto">
        <x-table.head>
            <x-table.header sortField="customer_name">
                Name
            </x-table.header>
            <x-table.header>
                Owner
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
                <x-table.header>{{$product->product_name}}
                    <br> {{$product->product_weight_g}}g
                </x-table.header>
            @endforeach
            <x-table.header>
                Actions
            </x-table.header>
        </x-table.head>
        <x-table.body>
            @foreach($customers as $customer)
                <x-table.row wire:key="customer-{{$customer->customer_id}}">
                    <x-table.data>
                            <span class="text-accent text-base">
                                {{$customer->customer_name}}
                            </span>
                    </x-table.data>
                    <x-table.data>
                        {{$customer->customer_business_owner_name}}
                    </x-table.data>
                    <x-table.data>
                        {{$customer->customer_optional_name}}
                        <br>
                        {{$customer->customer_address_1}}
                        <br>
                        {{$customer->customer_address_2}}
                        <br>
                        {{$customer->customer_city}}
                        <br>
                        {{$customer->customer_postcode}}
                    </x-table.data>
                    <x-table.data>
                        @if($customer->customer_email)
                            <flux:link
                                href="mailto:{{$customer->customer_email}}"
                                class="text-white">{{$customer->customer_email}}</flux:link>
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
                    <x-table.data link>
                        <flux:link href="{{route('customers.edit', compact('customer'))}}" title="Edit customer">
                            <flux:icon.pencil-square/>
                        </flux:link>
                    </x-table.data>
                </x-table.row>
            @endforeach
        </x-table.body>
    </x-table.table>
</div>
