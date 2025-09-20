
<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Add new customer" />
        <x-error/>
        <x-success />
        <form action="{{route('customers.store')}}" method="POST" class="flex flex-col gap-4">
            @csrf
            {{--Customer name--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_name" text="Business Name"/>
                <x-form.form-input id="customer_name" name="customer_name" value="{{old('customer_name', '')}}" placeholder="Business Name"/>
            </x-form.form-wrapper>
            {{--trading name--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_trading_name" text="Trading Name"/>
                <x-form.form-input id="customer_trading_name" name="customer_trading_name" value="{{old('customer_trading_name', '')}}" placeholder="Trading Name"/>
            </x-form.form-wrapper>
            {{--business owner name--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_business_owner_name" text="Business Owner"/>
                <x-form.form-input type="text" id="customer_business_owner_name" name="customer_business_owner_name" value="{{old('customer_business_owner_name', '')}}" placeholder="Business Owner Name"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper>
                <x-form.form-label id="customer_optional_name" text="Name (optional)"/>
                <x-form.form-input id="customer_optional_name" name="customer_optional_name" value="{{old('customer_optional_name', '')}}" placeholder="Optional name"/>
            </x-form.form-wrapper>
            {{--Customer address--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_address_1" text="First Address Line"/>
                <x-form.form-input id="customer_address_1" name="customer_address_1" value="{{old('customer_address_1', '')}}" placeholder="First address line"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper>
                <x-form.form-label id="customer_address_2" text="Second Address Line (optional)"/>
                <x-form.form-input id="customer_address_2" name="customer_address_2" value="{{old('customer_address_2', '')}}" placeholder="Second address line (optional)"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper>
                <x-form.form-label id="customer_city" text="City"/>
                <x-form.form-input id="customer_city" name="customer_city" value="{{old('customer_city', '')}}" placeholder="City"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper>
                <x-form.form-label id="customer_postcode" text="Postcode"/>
                <x-form.form-input id="customer_postcode" name="customer_postcode" value="{{old('customer_postcode', '')}}" placeholder="Postcode"/>
            </x-form.form-wrapper>
            <input type="hidden" id="customer_country" name="customer_country" value="United Kingdom"/>
            {{--Customer email--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_email" text="Email (optional)"/>
                <x-form.form-input type="email" id="customer_email" name="customer_email" value="{{old('customer_email', '')}}" placeholder="Email (optional)"/>
            </x-form.form-wrapper>
            {{--Customer phone--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_phone" text="Phone (optional)"/>
                <x-form.form-input id="customer_phone" name="customer_phone" value="{{old('customer_phone', '')}}" placeholder="Phone (optional)"/>
            </x-form.form-wrapper>
            <flux:button variant="primary" type="submit">Add</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
