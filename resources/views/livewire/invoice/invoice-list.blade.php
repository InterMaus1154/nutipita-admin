<div class="space-y-4">
    <x-success/>
    <x-error/>
    <x-table.table>
        <x-table.head>
            <x-table.header wire:click="setSort('invoice_number')">
                <div class="flex items-center justify-center text-center cursor-pointer">
                    Invoice#
                    <flux:icon.arrows-up-down variant="mini"/>
                </div>
            </x-table.header>
            <x-table.header wire:click="setSort('customer')">
                <div class="flex items-center justify-center text-center cursor-pointer">
                    Customer
                    <flux:icon.arrows-up-down variant="mini"/>
                </div>
            </x-table.header>
            <x-table.header wire:click="setSort('invoice_status')">
                <div class="flex items-center justify-center text-center cursor-pointer">
                    Status
                    <flux:icon.arrows-up-down variant="mini"/>
                </div>
            </x-table.header>
            <x-table.header wire:click="setSort('invoice_issue_date')">
                <div class="flex items-center justify-center text-center cursor-pointer">
                    Issue Date
                    <flux:icon.arrows-up-down variant="mini"/>
                </div>
            </x-table.header>
            <x-table.header wire:click="setSort('invoice_due_date')">
                <div class="flex items-center justify-center text-center cursor-pointer">
                    Due Date
                    <flux:icon.arrows-up-down variant="mini"/>
                </div>
            </x-table.header>
            <x-table.header>
                Orders From
            </x-table.header>
            <x-table.header>
                Orders To
            </x-table.header>
            <x-table.header wire:click="setSort('invoice_total')">
                <div class="flex items-center justify-center text-center cursor-pointer">
                    Invoice Total
                    <flux:icon.arrows-up-down variant="mini"/>
                </div>
            </x-table.header>
            <x-table.header>
                Actions
            </x-table.header>
        </x-table.head>
        <x-table.body>
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
                        @if($invoice->invoice_status === "paid")
                            <flux:badge color="green" variant="solid">Paid</flux:badge>
                        @else
                            <flux:badge color="red" variant="solid">Unpaid</flux:badge>
                        @endif
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
    </x-table.table>
    <div>
        {{$invoices->onEachSide(3)->links(data: ['scrollTo' => false])}}
    </div>
</div>
