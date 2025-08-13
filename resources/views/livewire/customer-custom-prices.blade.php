@use(Illuminate\Support\Facades\Crypt)
<div class="flex flex-col gap-4">
    @if(!$hasCustomPrices)
        <em>---No prices set yet!---</em>
    @else
        <div class="flex">
            <x-table.table>
                <x-table.head>
                    <x-table.header>
                        Product Name
                    </x-table.header>
                    <x-table.header>
                        Customer Unit Price
                    </x-table.header>
                    <x-table.header>
                        Modified
                    </x-table.header>
                </x-table.head>
                <tbody>
                @foreach($customer->customPrices as $customPrice)
                    <x-table.row>
                        <x-table.data>{{$customPrice->product->product_name}}</x-table.data>
                        <x-table.data>@unitPriceFormat($customPrice->customer_product_price)</x-table.data>
                        <x-table.data>{{dateToCarbon($customPrice->updated_at, "Custom Price updated at", false)->toDateString()}}</x-table.data>
                        <x-table.data>
                            <flux:link class="cursor-pointer"
                                       wire:click="delete('{{Crypt::encrypt( $customPrice->customer_product_price_id)}}')"
                                       wire:confirm="Are you sure you want to delete this custom price? This action cannot be undone">
                                Delete
                            </flux:link>
                        </x-table.data>
                    </x-table.row>
                @endforeach
                </tbody>
            </x-table.table>
        </div>
    @endif
</div>
