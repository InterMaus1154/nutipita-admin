@use(App\Enums\InvoiceStatus)
<div class="space-y-4">
    <x-success/>
    <x-error/>
    {{--table on destkop--}}
    <div class="hidden sm:block">
        <x-table.table>
            <x-table.head>
                <x-table.header sort-field="invoice_number">
                    Invoice#
                </x-table.header>
                <x-table.header sort-field="customer">
                    Customer
                </x-table.header>
                <x-table.header sort-field="invoice_status">
                    Status
                </x-table.header>
                <x-table.header sort-field="invoice_issue_date">
                    Issue Date
                </x-table.header>
                <x-table.header sort-field="invoice_due_date">
                    Due Date
                </x-table.header>
                <x-table.header>
                    Orders From
                </x-table.header>
                <x-table.header>
                    Orders To
                </x-table.header>
                <x-table.header sort-field="invoice_total">
                    Invoice Total
                </x-table.header>
                <x-table.header>
                    Actions
                </x-table.header>
            </x-table.head>
            <x-table.body wire:loading.remove>
                @if($invoices->isEmpty())
                    <x-table.row wire:key="empty-row">
                        <x-table.data>No invoices for the current filter!</x-table.data>
                    </x-table.row>
                @endif
                @foreach($invoices as $invoice)
                    <x-table.row wire:key="invoice-{{$invoice->invoice_number}}">
                        <x-table.data>
                            INV-{{$invoice->invoice_number}}
                        </x-table.data>
                        <x-table.data>
                        <span class="text-accent text-base">
                        {{$invoice->customer->customer_name}}
                        </span>
                        </x-table.data>
                        <x-table.data>
                            <x-invoice.invoice-status-select :invoice="$invoice"/>
                        </x-table.data>
                        <x-table.data>
                            @dayDate($invoice->invoice_issue_date)
                        </x-table.data>
                        <x-table.data>
                            @dayDate($invoice->invoice_due_date)
                        </x-table.data>
                        <x-table.data>
                            {{$invoice->invoice_from ? dayDate($invoice->invoice_from) : "-"}}
                        </x-table.data>
                        <x-table.data>
                            {{$invoice->invoice_to ? dayDate($invoice->invoice_to) : "-"}}
                        </x-table.data>
                        <x-table.data>
                            @moneyFormat($invoice->invoice_total)
                        </x-table.data>
                        <x-table.data link>
                            <flux:link href="{{route('invoices.view-inline', compact('invoice'))}}">
                                <flux:icon.eye class="!inline"/>
                            </flux:link>
                            <flux:link href="{{route('invoices.download', compact('invoice'))}}">
                                <flux:icon.arrow-down-tray class="!inline"/>
                            </flux:link>
                            @if($invoice->invoice_status == "due")
                                <flux:link class="cursor-pointer"
                                           wire:click="markPaid({{$invoice->invoice_id}})">
                                    <flux:icon.exclamation-circle class="!inline"/>
                                </flux:link>
                            @else
                                <flux:link class="cursor-pointer"
                                           wire:click="markDue({{$invoice->invoice_id}})">
                                    <flux:icon.check-circle class="!inline"/>
                                </flux:link>
                            @endif
                            <flux:link class="cursor-pointer"
                                       wire:click="delete({{$invoice->invoice_id}})"
                                       wire:confirm="Are you sure to delete this ({{$invoice->invoice_number}}) invoice for {{$invoice->customer->customer_name}}? This action cannot be undone!"
                            >
                                <flux:icon.trash class="!inline"/>
                            </flux:link>
                        </x-table.data>
                    </x-table.row>
                @endforeach
            </x-table.body>
            <x-table.body class="hidden" wire:loading.class="table-row-group!">
                @for($i = 0; $i < 5; $i++)
                    <x-table.row>
                        @for($j = 0; $j < 9; $j++)
                            <x-table.data class="relative">
                                <div class="h-6 w-full animate-shine"></div>
                            </x-table.data>
                        @endfor
                    </x-table.row>
                @endfor
            </x-table.body>
        </x-table.table>
    </div>
    {{--cards on mobile--}}
    <div class="flex flex-col gap-4 sm:hidden" wire:loading.remove>
        @foreach($invoices as $invoice)
            {{--card wrapper--}}
            <x-invoice.mobile-invoice-card wire:key="invoice-mobile-card-{{$invoice->invoice_id}}" :invoice="$invoice"/>
        @endforeach
    </div>
    <div class="flex-col gap-4 hidden" wire:loading.class="max-sm:flex">
        @for($i = 0; $i<5;$i++)
            <x-ui.mobile-card-skeleton class="h-[175px]">
                <div class="h-10 w-full animate-shine"></div>
            </x-ui.mobile-card-skeleton>
        @endfor
    </div>
    <div>
        {{$invoices->onEachSide(3)->links(data: ['scrollTo' => false])}}
    </div>
</div>
