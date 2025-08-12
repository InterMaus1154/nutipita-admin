<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Edit Customer '{{$customer->customer_name}}'"/>
        <x-error/>
        <x-success/>
        <form action="{{route('customers.update', compact('customer'))}}" method="POST" class="flex flex-col gap-4">
            @csrf
            @method('PUT')
            {{--Customer name--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_name" text="Business Name"/>
                <x-form.form-input id="customer_name" name="customer_name" value="{{$customer->customer_name}}"/>
            </x-form.form-wrapper>
            {{--business owner name--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_business_owner_name" text="Business Owner"/>
                <x-form.form-input type="text" id="customer_business_owner_name" name="customer_business_owner_name"
                                   value="{{$customer->customer_business_owner_name}}"
                                   placeholder="Business Owner Name"/>
            </x-form.form-wrapper>
            {{--address 1--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_address_1" text="First Address Line"/>
                <x-form.form-input id="customer_address_1" name="customer_address_1" value="{{$customer->customer_address_1}}" />
            </x-form.form-wrapper>
            {{--address 2--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_address_2" text="Second Address Line (optional)"/>
                <x-form.form-input id="customer_address_2" name="customer_address_2" value="{{$customer->customer_address_2}}"/>
            </x-form.form-wrapper>
            {{--postcode--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_postcode" text="Postcode"/>
                <x-form.form-input id="customer_postcode" name="customer_postcode" value="{{$customer->customer_postcode}}"/>
            </x-form.form-wrapper>
            {{--city--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_city" text="City"/>
                <x-form.form-input id="customer_city" name="customer_city" value="{{$customer->customer_city}}"/>
            </x-form.form-wrapper>
            <div class="hidden">
                <label for="customer_country" class="block text-sm font-medium mb-2 dark:text-white">Country</label>
                <input type="hidden" id="customer_country" name="customer_country" value="United Kingdom"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            {{--email--}}
            <x-form.form-wrapper>
                <x-form.form-label id="customer_email" text="Email (optional)"/>
                <x-form.form-input type="email" id="customer_email" name="customer_email" value="{{$customer->customer_email}}"/>
            </x-form.form-wrapper>
            {{--phone--}}
            <x-form.form-wrapper>
                 <x-form.form-label id="customer_phone" text="Phone (optional)"/>
                <x-form.form-input id="customer_phone" name="customer_phone" value="{{$customer->customer_phone}}"/>
            </x-form.form-wrapper>
            <flux:button variant="primary" type="submit">Update</flux:button>
        </form>
    </x-page-section>
</x-flux-layout>
