<x-layout>
    <div class="form-wrapper">
        <h2 class="form-title">Update customer</h2>
        <x-error />
        <form action="{{route('customers.update', compact('customer'))}}" method="POST">
            @csrf
            @method('PUT')
            {{--Customer name--}}
            <div class="input-wrapper">
                <label for="customer_name">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" value="{{$customer->customer_name}}" />
            </div>
            {{--Customer address--}}
            <div class="input-wrapper">
                <label for="customer_address">Customer Address (optional)</label>
                <input type="text" id="customer_address" name="customer_address" value="{{$customer->customer_address}}" />
            </div>
            {{--Customer email--}}
            <div class="input-wrapper">
                <label for="customer_email">Customer Email (optional)</label>
                <input type="email" id="customer_email" name="customer_email" value="{{$customer->customer_email}}" />
            </div>
            {{--Customer phone--}}
            <div class="input-wrapper">
                <label for="customer_phone">Customer Phone (optional)</label>
                <input type="text" id="customer_phone" name="customer_phone" value="{{$customer->customer_phone}}" />
            </div>
            <input type="submit" value="Edit" class="form-submit-button">
        </form>
    </div>
</x-layout>
