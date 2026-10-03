<div class="grid grid-cols-2  justify-center gap-4 2xl:max-w-[50%] mx-auto">
    <x-data-box data-box-header="#" :data-box-value="numberFormat($invoiceCount, 0)"/>
    <x-data-box data-box-header="Invoices Total" :data-box-value="moneyFormat($invoiceTotals)"/>
    @if($unpaidSum > 0)
        <x-data-box data-box-header="Unpaid Sum" :data-box-value="moneyFormat($unpaidSum)"/>
    @endif
    <p>hello</p>
</div>
