<div class="space-y-4">
    @if($orders->isNotEmpty())
        <div class="space-y-4">
            <div @class([
                        'hidden' => !$visible,
                        'grid grid-cols-2 sm:grid-cols-[repeat(auto-fill,minmax(min(200px,100%),1fr))] gap-2 sm:gap-4 mx-auto'
                    ])>
                    @if($withIncome)
                        <x-data-box dataBoxHeader="Income" :dataBoxValue="moneyFormat($totalIncome)"/>
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

