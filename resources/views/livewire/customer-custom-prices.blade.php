<div class="flex flex-col gap-4">
    <flux:link href="{{route('customers.edit.custom-price', compact('customer'))}}">Edit custom
        prices
    </flux:link>
    @if(!$hasCustomPrices)
        <em>---No prices set yet!---</em>
    @else
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="divide-y divide-gray-200 dark:divide-neutral-700">
                            <thead>
                            <tr >
                                <th scope="col" class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">Product Name</th>
                                <th scope="col" class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">Customer Unit Price</th>
                                <th scope="col" class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">Modified</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($customer->customPrices as $customPrice)
                                <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{$customPrice->product->product_name}}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">@formatMoneyPound($customPrice->customer_product_price)</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{\Illuminate\Support\Carbon::parse($customPrice->created_at)->toDateString()}}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                        <flux:link
                                            wire:click="delete('{{\Illuminate\Support\Facades\Crypt::encrypt( $customPrice->customer_product_price_id)}}')"
                                            wire:confirm="Are you sure you want to delete this custom price?">Delete
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
    @endif
</div>
