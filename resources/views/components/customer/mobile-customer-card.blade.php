@props(['customer'])
<x-ui.mobile-card-skeleton>
    @php
        /**
* @var \App\Models\Customer $customer
 */
    @endphp
    {{--card header--}}
    <div class="grid grid-cols-[1fr_auto_1fr] gap-2">
        <div></div>
        <div class="justify-self-center text-center">
            <span class="text-lg text-center font-bold text-accent">
            {{$customer->customer_name}}
            </span>
        </div>
        <x-ui.mobile-card-dropdown-menu class="justify-self-end">
            <x-ui.mobile-card-dropdown-link href="{{route('customers.edit', compact('customer'))}}">Edit
            </x-ui.mobile-card-dropdown-link>
        </x-ui.mobile-card-dropdown-menu>
    </div>
    {{--card body--}}
    <div class="flex flex-col gap-2 flex-wrap">
        @if($customer->customer_business_owner_name)
            <div class="flex gap-2 items-center">
                <flux:icon.user-circle class="text-accent size-5"/>
                <span>{{$customer->customer_business_owner_name}}</span>
            </div>
        @endif
        @if($customer->customer_phone)
            <div class="flex gap-2 items-center">
                <flux:icon.phone class="text-accent size-5"/>
                <flux:link href="tel:{{$customer->customer_phone}}" class="text-black dark:text-white">{{$customer->customer_phone}}</flux:link>
            </div>
        @endif
        @if($customer->customer_email)
            <div class="flex gap-2 items-center">
                <flux:icon.envelope class="text-accent size-5"/>
                <flux:link href="mailto:{{$customer->customer_email}}" class="text-black dark:text-white">{{$customer->customer_email}}</flux:link>
            </div>
        @endif
        <div class="flex gap-2 items-center">
            <flux:icon.home-modern class="text-accent size-5"/>
            <span>
                {{$customer->customer_optional_name}}
                {{$customer->customer_address_1}}
                {{$customer->customer_address_2}}
                {{$customer->customer_city}}
                {{$customer->customer_postcode}}
            </span>
        </div>
    </div>
    <x-ui.detail-popup-card>
        <div class="flex justify-center items-center">
            <span class="text-accent font-bold text-lg">{{$customer->customer_name}}</span>
        </div>
        <div class="flex flex-col gap-4">
            {{--customer custom prices--}}
            @foreach($customer->customPrices as $customPrice)
                @if($loop->first)
                    <flux:separator/>
                @endif
                <div class="text-base flex gap-4 justify-between items-center">
                    <span>{{$customPrice->product->product_name}}</span>
                    <span>@unitPriceFormat($customPrice->customer_product_price)</span>
                </div>
                <flux:separator/>
            @endforeach
        </div>
        <div class="flex gap-6 justify-center">
            <flux:link href="{{route('customers.edit', compact('customer'))}}">
                <flux:icon.pencil-square class="size-7"/>
            </flux:link>
        </div>
    </x-ui.detail-popup-card>
</x-ui.mobile-card-skeleton>
