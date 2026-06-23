<div class="flex flex-col gap-4 sm:hidden">
    @foreach($orders as $order)
        <x-order.mobile-order-card wire:key="order-mobile-card-{{$order->order_id}}" :order="$order"/>
    @endforeach
</div>
