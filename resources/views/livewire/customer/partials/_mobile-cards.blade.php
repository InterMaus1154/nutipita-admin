<div class="flex flex-col gap-4">
    @foreach($customers as $customer)
        <x-customer.mobile-customer-card wire:key="customer-mobile-card-{{$customer->customer_id}}" :customer="$customer"/>
    @endforeach
</div>
