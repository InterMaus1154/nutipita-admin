<div>
    @if(!is_null($invoiceId))
        <x-ui.detail-popup-card>
            <div class="flex justify-center">
                <span class="text-lg text-accent text-center font-bold">
                    {{$invoice->customer->customer_name}}
                </span>
            </div>
            <div class="flex justify-between gap-4 items-center">
                <div class="flex gap-2 items-center">
                    <flux:icon.notebook-tabs class="size-5 text-accent"/>
                    <span class="text-lg font-semibold">INV-{{$invoice->invoice_number}}</span>
                </div>
                <div class="flex gap-2 items-center">
                    <flux:icon.circle-pound-sterling class="size-5 text-accent"/>
                    <span class="text-white text-lg font-semibold">@moneyFormat($invoice->invoice_total)</span>
                </div>
            </div>
            <div class="flex justify-between gap-4">
                <div class="flex gap-2 flex-col justify-center text-center">
                    <span>Issued At:</span>
                    <span class="text-base">@dayDate($invoice->invoice_issue_date)</span>
                </div>
                <div class="flex gap-2 flex-col justify-center text-center">
                    <span>Due At:</span>
                    <span class="text-base">@dayDate($invoice->invoice_due_date)</span>
                </div>
            </div>
            <div class="flex justify-between gap-4">
                <div class="flex gap-2 flex-col justify-center text-center">
                    <span>Orders From:</span>
                    <span class="text-base">{{$invoice->invoice_from ? dayDate($invoice->invoice_from) : '-'}}</span>
                </div>
                <div class="flex gap-2 flex-col justify-center text-center">
                    <span>Orders To:</span>
                    <span class="text-base">{{$invoice->invoice_to ? dayDate($invoice->invoice_to) : '-'}}</span>
                </div>
            </div>
            <div class="flex flex-col gap-4 my-4">
                @foreach($invoice->products as $invoiceProduct)
                    @if($loop->first)
                        <flux:separator/>
                    @endif
                    <div class="text-base flex gap-4 justify-between items-center">
                        <span>{{$invoiceProduct->product->product_name}} {{$invoiceProduct->product->product_weight_g}}g</span>
                        <div class="flex flex-col gap-1 justify-center items-center text-center">
                            <span>@amountFormat($invoiceProduct->product_qty) x @unitPriceFormat($invoiceProduct->product_unit_price)</span>
                            <span>@moneyFormat($invoiceProduct->product_qty * $invoiceProduct->product_unit_price)</span>
                        </div>
                    </div>
                    <flux:separator/>
                @endforeach
            </div>
        </x-ui.detail-popup-card>
    @endif
</div>
