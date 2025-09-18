@props(['invoice'])
@use(App\Enums\InvoiceStatus)
<x-ui.mobile-card-skeleton>
    @php
        /**
* @var \App\Models\Invoice $invoice
 */
    @endphp
    {{--card header--}}
    <div class="grid grid-cols-[1fr_auto_1fr]">
        <div class="justify-self-start">
            <x-invoice.invoice-status-select :invoice="$invoice"/>
        </div>
        <div class="justify-self-center text-center ml-2">
            <span class="text-lg text-accent font-bold">
            {{$invoice->customer->customer_name}}
            </span>
        </div>
        <x-ui.mobile-card-dropdown-menu class="justify-self-end">
            <x-ui.mobile-card-dropdown-link href="{{route('invoices.view-inline', compact('invoice'))}}">View PDF
            </x-ui.mobile-card-dropdown-link>
            <x-ui.mobile-card-dropdown-link href="{{route('invoices.download', compact('invoice'))}}">Download PDF
            </x-ui.mobile-card-dropdown-link>
            @if($invoice->invoice_status == "due")
                <x-ui.mobile-card-dropdown-link wire:click="markPaid({{$invoice->invoice_id}})">Mark Paid
                </x-ui.mobile-card-dropdown-link>
            @else
                <x-ui.mobile-card-dropdown-link wire:click="markDue({{$invoice->invoice_id}})">Mark Unpaid
                </x-ui.mobile-card-dropdown-link>
            @endif
        </x-ui.mobile-card-dropdown-menu>
    </div>
    <div class="flex justify-between gap-4">
        <div class="flex gap-2 items-center">
            <flux:icon.notebook-tabs class="size-5 text-accent"/>
            <span class="text-lg font-semibold">
            INV-{{$invoice->invoice_number}}
            </span>
        </div>
        <div class="flex gap-2 items-center">
            <flux:icon.circle-pound-sterling class="size-5 text-accent"/>
            <span class="font-semibold text-lg">
                @moneyFormat($invoice->invoice_total)
            </span>
        </div>
    </div>
    <x-ui.detail-popup-card>
        <div class="grid grid-cols-[1fr_auto_1fr] gap-2">
            <div class="justify-self-start">
                <x-invoice.invoice-status-select :invoice="$invoice"/>
            </div>
            <div class="justify-self-center text-center">
                <span class="text-lg text-accent text-center font-bold">
                {{$invoice->customer->customer_name}}
            </span>
            </div>
            <div></div>
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
        <div class="flex gap-6 justify-center justify-self-end">
            <flux:link href="{{route('invoices.view-inline', compact('invoice'))}}">
                <flux:icon.eye class="size-7"/>
            </flux:link>
            <flux:link href="{{route('invoices.download', compact('invoice'))}}">
                <flux:icon.arrow-down-tray class="size-7"/>
            </flux:link>
            @if($invoice->invoice_status == "due")
                <flux:link class="cursor-pointer"
                           wire:click="markPaid({{$invoice->invoice_id}})">
                    <flux:icon.exclamation-circle class="size-7"/>
                </flux:link>
            @else
                <flux:link class="cursor-pointer"
                           wire:click="markDue({{$invoice->invoice_id}})">
                    <flux:icon.check-circle class="size-7"/>
                </flux:link>
            @endif
            <flux:link class="cursor-pointer"
                       wire:click="delete({{$invoice->invoice_id}})"
                       wire:confirm="Are you sure to delete this ({{$invoice->invoice_number}}) invoice for {{$invoice->customer->customer_name}}? This action cannot be undone!"
            >
                <flux:icon.trash class="size-7"/>
            </flux:link>
        </div>
    </x-ui.detail-popup-card>
</x-ui.mobile-card-skeleton>
