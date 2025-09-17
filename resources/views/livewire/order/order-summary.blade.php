<div class="space-y-4">
    @if(!$disabled && $ordersCount > 0 )
        <div class="space-y-4">
            <div @class([
                        'hidden' => !$visible,
//                        'grid grid-cols-2 sm:grid-cols-[repeat(auto-fill,minmax(min(200px,100%),1fr))] gap-2 sm:gap-4 mx-auto',
'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-[repeat(auto-fit,minmax(0,1fr))] justify-center gap-4 2xl:max-w-[65%] mx-auto'
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
        </div>
    @endif
</div>

