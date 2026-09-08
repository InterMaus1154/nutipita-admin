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
            <x-ui.mobile-card-dropdown-link wire:click="toggleCustomerVisibility({{$customer->getId()}})">
                @if($customer->getIsHidden())
                    Show
                @else
                    Hide
                @endif
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
                <flux:link href="tel:{{$customer->customer_phone}}"
                           class="text-black dark:text-white">{{$customer->customer_phone}}</flux:link>
            </div>
        @endif
        @if($customer->customer_email)
            <div class="flex gap-2 items-center">
                <flux:icon.envelope class="text-accent size-5"/>
                <flux:link href="mailto:{{$customer->customer_email}}"
                           class="text-black dark:text-white">{{$customer->customer_email}}</flux:link>
            </div>
        @endif
        <div class="flex gap-2 items-center">
            <flux:icon.home-modern class="text-accent size-5"/>
            <span>
                <span class="font-bold">{{$customer->customer_trading_name}}</span>
                {{$customer->customer_optional_name}}
                {{$customer->customer_address_1}}
                {{$customer->customer_address_2}}
                {{$customer->customer_city}}
                {{$customer->customer_postcode}}
            </span>
        </div>
        @if($customer->customer_delivery_address)
            <div class="flex gap-2 items-center">
                <flux:icon.truck class="text-accent size-5"/>
                <span class="max-w-[40ch] whitespace-normal break-words">{{$customer->customer_delivery_address}}</span>
            </div>
        @endif
    </div>
    <flux:button
        @click="$dispatch('modal-open', { component: 'customer.customer-popup-card', componentData: { customerId: {{$customer->customer_id}} } })">
        <flux:icon.chevron-double-up class="text-accent"/>
    </flux:button>
</x-ui.mobile-card-skeleton>
