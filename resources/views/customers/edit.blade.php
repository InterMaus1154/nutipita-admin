<x-layout>
    <div class="form-wrapper">
        <h2 class="form-title">Update customer</h2>
        <x-error/>
        <form action="{{route('customers.update', compact('customer'))}}" method="POST">
            @csrf
            @method('PUT')
            {{--Customer name--}}
            <div class="input-wrapper">
                <label for="customer_name">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" value="{{$customer->customer_name}}"/>
            </div>
            {{--Customer address--}}
            <div class="input-wrapper">
                <label for="customer_address_1">Customer Address Line 1</label>
                <input type="text" id="customer_address_1" name="customer_address_1" value="{{$customer->customer_address_1}}" />
            </div>
            <div class="input-wrapper">
                <label for="customer_address_2">Customer Address Line 2 (optional)</label>
                <input type="text" id="customer_address_2" name="customer_address_2" value="{{$customer->customer_address_2}}" />
            </div>
            <div class="input-wrapper">
                <label for="customer_postcode">Customer Postcode</label>
                <input type="text" id="customer_postcode" name="customer_postcode" value="{{$customer->customer_postcode}}" />
            </div>
            <div class="input-wrapper">
                <label for="customer_city">Customer City</label>
                <input type="text" id="customer_city" name="customer_city" value="{{$customer->customer_city}}" />
            </div>
            <div class="input-wrapper">
                <label for="customer_country">Customer Country</label>
                <input type="text" id="customer_country" name="customer_country" value="{{$customer->customer_country}}" />
            </div>
            {{--Customer email--}}
            <div class="input-wrapper">
                <label for="customer_email">Customer Email (optional)</label>
                <input type="email" id="customer_email" name="customer_email" value="{{$customer->customer_email}}"/>
            </div>
            {{--Customer phone--}}
            <div class="input-wrapper">
                <label for="customer_phone">Customer Phone (optional)</label>
                <input type="text" id="customer_phone" name="customer_phone" value="{{$customer->customer_phone}}"/>
            </div>
            <input type="submit" value="Update" class="form-submit-button">
        </form>
    </div>
</x-layout>
