<div class="space-y-4">
    @if($orders->isNotEmpty())
        <div class="space-y-4">
            <div @class([
                        'hidden' => !$visible,
//                        'grid grid-cols-2 sm:grid-cols-[repeat(auto-fill,minmax(min(200px,100%),1fr))] gap-2 sm:gap-4 mx-auto',
'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-[repeat(auto-fit,minmax(0,1fr))] justify-center gap-4 2xl:max-w-[65%] mx-auto'
                    ])>
                @if($withIncome)
                    <x-data-box dataBoxHeader="Paid" :dataBoxValue="moneyFormat($totalIncome)"/>
                    <x-data-box dataBoxHeader="Expected" :dataBoxValue="moneyFormat($expectedIncome)"/>
                @endif
                <x-data-box dataBoxHeader="Orders" :dataBoxValue="amountFormat(collect($orders)->count())"/>
                <x-data-box dataBoxHeader="Pita" :dataBoxValue="amountFormat($totalPita)"/>

                @foreach($productTotals as $productTotal)
                    @unless(empty($productTotal['qty']))
                        <x-data-box wire:key="$productTotal['name']"
                                    :dataBoxHeader="$productTotal['name']"
                                    :dataBoxValue="amountFormat($productTotal['qty'])"
                                    :subtitle="$productTotal['g'].'g'"
                        />
                    @endunless
                @endforeach
            </div>
        </div>
    @endif
</div>

