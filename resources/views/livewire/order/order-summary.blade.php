<div class="space-y-4">
    @if(!$disabled && $ordersCount > 0 )
        <div class="space-y-4">
            <div wire:loading.remove @class([
                        'hidden' => !$visible,
'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-[repeat(auto-fit,minmax(200px,1fr))] justify-center gap-4 2xl:max-w-[90%] mx-auto'
                    ])>
                @if($withIncome)
                    <x-data-box dataBoxHeader="Income" :dataBoxValue="moneyFormat($totalIncome)"/>
                @endif
                <x-data-box dataBoxHeader="Orders" :dataBoxValue="amountFormat($ordersCount)"/>
                <x-data-box dataBoxHeader="Pita" :dataBoxValue="amountFormat($totalPita)"/>

                @foreach($productTotals as $productTotal)
                    <x-data-box wire:key="$productTotal->product_id"
                                :dataBoxHeader="$productTotal->product_name"
                                :dataBoxValue="amountFormat($productTotal->product_qty)"
                                :subtitle="$productTotal->product_weight_g.'g'"
                    />
                @endforeach
            </div>
            @if($hasLoading)
                <div
                    class="hidden grid-cols-2 md:grid-cols-3 lg:grid-cols-[repeat(auto-fit,minmax(200px,1fr))] justify-center gap-4 2xl:max-w-[90%] mx-auto"
                    wire:loading.grid>
                    @for($i=0;$i<4;$i++)
                        <x-data-box class="h-[112px]">
                            <div class="h-full w-full animate-shine"></div>
                        </x-data-box>
                    @endfor
                </div>
            @endif
        </div>
    @endif
</div>

