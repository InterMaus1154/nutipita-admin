<div class="space-y-4">
    @if($orders->isNotEmpty())
        <div class="space-y-4">
            <flux:button wire:click="toggleSummaries()">Toggle Summaries</flux:button>
            <div @class([
                        'hidden' => !$visible,
                        'flex flex-col gap-4'
                    ])>
                <div class="grid grid-cols-3 gap-4 sm:flex sm:gap-6 flex-wrap">
                    @if($withIncome)
                        <x-data-box dataBoxHeader="Total Income" :dataBoxValue="moneyFormat($totalIncome)"/>
                    @endif
                    <x-data-box dataBoxHeader="Total Orders" :dataBoxValue="amountFormat(collect($orders)->count())"/>
                    <x-data-box dataBoxHeader="Total Pita" :dataBoxValue="amountFormat($totalPita)"/>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:flex sm:gap-6 flex-wrap">
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


