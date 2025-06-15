<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Customers"/>
        <x-error/>
        <x-success/>
        <flux:link href="{{route('customers.create')}}">Add new customer</flux:link>
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead>
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    ID
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Name
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Address
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Email
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Phone
                                </th>
                                @foreach($products as $product)
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">{{$product->product_name}}</th>
                                @endforeach
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Total Orders
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($customers as $customer)
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        <flux:link
                                            href="{{route('customers.show', compact('customer'))}}">{{$customer->customer_id}}</flux:link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        <flux:link
                                            href="{{route('customers.show', compact('customer'))}}">{{$customer->customer_name}}</flux:link>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        {{$customer->short_address}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        @if($customer->customer_email)
                                            <flux:link
                                                href="mailto:{{$customer->customer_email}}">{{$customer->customer_email}}</flux:link>
                                        @else
                                            <em>No email provided!</em>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        @if($customer->customer_phone)
                                            <flux:link
                                                href="tel:{{$customer->customer_phone}}">{{$customer->customer_phone}}</flux:link>
                                        @else
                                            <em>No phone provided!</em>
                                        @endif
                                    </td>
                                    @foreach($products as $product)
                                        @php
                                            $product = $product->setCurrentCustomer($customer);
                                        @endphp
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                            {{$product->price === 0 ? "" : "£".$product->price}}
                                        </td>
                                    @endforeach
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        {{$customer->orders_count}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200 space-x-1.5">
                                        <flux:link href="{{route('customers.show', compact('customer'))}}">View
                                        </flux:link>
                                        <flux:link href="{{route('customers.edit', compact('customer'))}}">Edit
                                        </flux:link>
                                        <flux:link href="{{route('customers.edit.custom-price', compact('customer'))}}">
                                            Edit Prices
                                        </flux:link>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </x-page-section>
</x-flux-layout>
