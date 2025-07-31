<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Customer '{{$customer->customer_name}}'" details/>
        <x-success/>
        <x-error/>
        <flux:link href="{{route('customers.edit', compact('customer'))}}">Edit customer</flux:link>
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 inline-block align-middle">
                    <div class="overflow-hidden">
                        <table
                            class="divide-y divide-gray-200 dark:divide-neutral-700 border border-zinc-600 border-solid">
                            <tbody>
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">Full Name/Company name</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{$customer->customer_name}}</td>
                            </tr>
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">Address Line 1</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    {{$customer->customer_address_1}}
                                </td>
                            </tr>
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">Address Line 2</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    @if($customer->customer_address_2)
                                        {{$customer->customer_address_2}}
                                    @else
                                        <em>Not provided</em>
                                    @endif
                                </td>
                            </tr>
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">City</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{$customer->customer_city}}</td>
                            </tr>
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">Postcode</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{$customer->customer_postcode}}</td>
                            </tr>
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">Email</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    @if($customer->customer_email)
                                        <a class="action-link"
                                           href="mailto:{{$customer->customer_email}}">{{$customer->customer_email}}</a>
                                    @else
                                        <em>No email provided</em>
                                    @endif
                                </td>
                            </tr>
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">Phone</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
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
                </div>
            </div>
        </div>
        {{--custom prices section--}}
        <h3 class="font font-bold">Custom prices</h3>
        @livewire('customer-custom-prices', ['customer' => $customer])
        {{--custom orders section--}}
        <h3 class="font font-bold">Customer Orders</h3>
        <flux:link href="{{route('orders.create', ['customer_id' => $customer->customer_id])}}">Place New
            Order
        </flux:link>
        @if(collect($customer->orders)->isEmpty())
            <em>This customer hasn't placed an order yet!</em>
        @else
            <livewire:order-list :filters="['customer_id' => $customer->customer_id, 'cancelled_order_hidden' => false]" />
        @endif
    </x-page-section>
</x-flux-layout>
