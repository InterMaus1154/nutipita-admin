@props(['invoice'])
@use(App\Enums\InvoiceStatus)
<x-ui.mobile-card-skeleton>
    @php
        /**
* @var \App\Models\Invoice $invoice
 */
    @endphp
    <div class="grid grid-cols-[1fr_auto_1fr]">
        <div class="justify-self-start">
            <x-invoice.invoice-status-select :invoice="$invoice"/>
        </div>
        <div class="justify-self-center text-center ml-2">
                <span class="text-lg text-accent font-semibold">
                    INV-{{$invoice->invoice_number}}
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
            <x-ui.mobile-card-dropdown-link wire:click="delete({{$invoice->invoice_id}})" wire:confirm="{{sprintf('Are you sure to delete this invoice %s for %s? This cannot be undone', $invoice->invoice_number, $invoice->customer->customer_name)}} ">
                Delete Invoice
            </x-ui.mobile-card-dropdown-link>
        </x-ui.mobile-card-dropdown-menu>
    </div>
    <div class="flex justify-between gap-4">
        <div class="flex gap-2 items-center">
            <flux:icon.user-circle class="size-5 text-accent" />
            <span class="text-lg font-semibold">
                    {{$invoice->customer->customer_name}}
        </span>
        </div>
        <div class="flex gap-2 items-center">
            <flux:icon.circle-pound-sterling class="size-5 text-accent"/>
            <span class="font-semibold text-lg">
                @moneyFormat($invoice->invoice_total)
            </span>
        </div>
    </div>
    <flux:button @click="$dispatch('modal-open', { component: 'invoice.invoice-popup-card', componentData: { invoiceId: {{$invoice->invoice_id}} } })">
        <flux:icon.chevron-double-up class="text-accent"/>
    </flux:button>
</x-ui.mobile-card-skeleton>
