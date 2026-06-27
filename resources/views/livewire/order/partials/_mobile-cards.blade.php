<div class="flex flex-col gap-4 sm:hidden">
    @foreach($mobileOrders as $orderData)
        <x-order.mobile-order-card wire:key="order-mobile-card-{{$orderData['order_id']}}"
                                   :order="(object) $orderData"/>
    @endforeach

    @if($mobileHasMore)
        <div x-data="{
            observe(){
            const el = this.$el;
            const observer = new IntersectionObserver((entries) => {
                if(entries[0].isIntersecting){
                    $wire.loadMore();
                }
            });

            observer.observe(el);
            this._observer = observer;

            }
        }"
             x-init="observe()"
             x-effect="if (!$wire.mobileLoading){ observe() }"
             class="flex justify-center py-6"
        >
        </div>
    @endif
</div>
