<div class="flex flex-col gap-4 sm:hidden">
    @foreach($invoices as $invoice)
        <x-invoice.mobile-invoice-card wire:key="invoice-mobile-card-{{$invoice->invoice_id}}" :invoice="$invoice"/>
    @endforeach
</div>
