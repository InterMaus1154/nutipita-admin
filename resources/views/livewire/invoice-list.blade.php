<div class="space-y-4">
    <x-success />
    <x-error />
    <div>
        {{$invoices->onEachSide(3)->links(data: ['scrollTo' => false])}}
    </div>
    <x-table.table>
        <x-table.head>
                <x-table.header>
                    Invoice#
                </x-table.header>
                <x-table.header>
                    Customer
                </x-table.header>
                <x-table.header>
                    Issue Date
                </x-table.header>
                <x-table.header>
                    Due Date
                </x-table.header>
                <x-table.header>
                    Status
                </x-table.header>
                <x-table.header>
                    Orders From
                </x-table.header>
                <x-table.header>
                    Orders To
                </x-table.header>
                <x-table.header>
                    Invoice Total - £
                </x-table.header>
                <x-table.header>
                    Actions
                </x-table.header>
        </x-table.head>
        <x-table.body>
        @foreach($invoices as $invoice)
            <x-table.row>
                <x-table.data>
                    INV-{{$invoice->invoice_number}}
                </x-table.data>
                <x-table.data>
                    <flux:link
                        href="{{route('customers.show', ['customer' => $invoice->customer])}}">{{$invoice->customer->customer_name}}</flux:link>
                </x-table.data>
                <x-table.data>
                    @dayDate($invoice->invoice_issue_date)
                </x-table.data>
                <x-table.data>
                    @dayDate($invoice->invoice_due_date)
                </x-table.data>
                <x-table.data>
                    @if($invoice->invoice_status === "paid")
                        <flux:badge color="green">Paid</flux:badge>
                    @else
                        <flux:badge color="yellow">Due</flux:badge>
                    @endif
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
                <x-table.data>
                    <flux:link href="{{route('invoices.view-inline', compact('invoice'))}}">View PDF
                    </flux:link>
                    <flux:link href="{{route('invoices.download', compact('invoice'))}}">Download
                    </flux:link>
                    @if($invoice->invoice_status == "due")
                        <flux:link class="cursor-pointer"
                                   wire:click="markPaid({{$invoice->invoice_id}})">Mark Paid
                        </flux:link>
                    @else
                        <flux:link class="cursor-pointer"
                                   wire:click="markDue({{$invoice->invoice_id}})">Mark Due
                        </flux:link>
                    @endif
                    <flux:link class="cursor-pointer"
                               wire:click="delete({{$invoice->invoice_id}})"
                               wire:confirm="Are you sure to delete this ({{$invoice->invoice_number}}) invoice for {{$invoice->customer->customer_name}}? This action cannot be undone!"
                    >Delete
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
