<div class="space-y-4">
    @if($orders->isNotEmpty())
        <div class="space-y-4">
            <flux:button wire:click="toggleSummaries()" class="sm:hidden">Toggle Summaries</flux:button>
            <div @class([
                        'hidden' => !$visible,
                        'flex gap-4 justify-between flex-wrap'
                    ])>
                <div class="flex gap-4">
                    @if($withIncome)
                        <x-data-box dataBoxHeader="Total Income" :dataBoxValue="moneyFormat($totalIncome)"/>
                    @endif
                    <x-data-box dataBoxHeader="Total Orders" :dataBoxValue="amountFormat(collect($orders)->count())"/>
                    <x-data-box dataBoxHeader="Total Pita" :dataBoxValue="amountFormat($totalPita)"/>
                </div>
                <div class="flex gap-4">
                    @foreach($productTotals as $productTotal)
                        @unless(empty($productTotal['qty']))
                            <x-data-box wire:key="$productTotal['name']"
                                        :dataBoxHeader="$productTotal['name'].' '.$productTotal['g'].'g'"
                                        :dataBoxValue="amountFormat($productTotal['qty'])"/>
                        @endunless
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

