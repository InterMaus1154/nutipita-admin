<x-layout>
<div class="form-wrapper">
    <h2 class="form-title">Add new customer</h2>
    <x-error />
    <form action="{{route('customers.store')}}" method="POST">
        @csrf
        {{--Customer name--}}
        <div class="input-wrapper">
            <label for="customer_name">Customer Name</label>
            <input type="text" id="customer_name" name="customer_name" value="{{old('customer_name', '')}}" />
        </div>
        {{--Customer email--}}
        <div class="input-wrapper">
            <label for="customer_email">Customer Email (optional)</label>
            <input type="email" id="customer_email" name="customer_email" value="{{old('customer_email', '')}}" />
        </div>
        {{--Customer phone--}}
        <div class="input-wrapper">
            <label for="customer_phone">Customer Phone (optional)</label>
            <input type="text" id="customer_phone" name="customer_phone" value="{{old('customer_phone', '')}}" />
        </div>
        <input type="submit" value="Add" class="form-submit-button">
    </form>
</div>
</x-layout>
