<div class="table-wrapper">
    <table>
        <thead>
        <tr>
            <th>Invoice#</th>
            <th>Customer</th>
            <th>Invoice Issue Date</th>
            <th>Invoice Due Date</th>
            <th>Invoice Status</th>
            <th>Orders From</th>
            <th>Orders To</th>
        </tr>
        </thead>
        <tbody>
        @forelse($invoices as $invoice)
            <tr>
                <td>INV-{{$invoice->invoice_number}}</td>
                <td>{{$invoice->customer->customer_name}}</td>
                <td>{{dayDate($invoice->invoice_issue_date)}}</td>
                <td>{{dayDate($invoice->invoice_due_date)}}</td>
                <td>{{ucfirst($invoice->invoice_status)}}</td>
                <td>{{$invoice->invoice_from ?? "-"}}</td>
                <td>{{$invoice->invoice_to ?? "-"}}</td>
                <td>
                    <a href="{{route('invoices.download', compact('invoice'))}}" class="action-link table">Download</a>
                    @if($invoice->invoice_status == "due")
                        <a href="#" class="action-link table"
                           wire:click="markPaid({{$invoice->invoice_id}})">Mark Paid
                        </a>
                    @else
                        <a href="#" class="action-link table"
                           wire:click="markDue({{$invoice->invoice_id}})">Mark Due
                        </a>
                    @endif
                    <a href="#" class="action-link table"
                       wire:click="delete({{$invoice->invoice_id}})"
                       wire:confirm="Are you sure to delete this invoice?"
                    >Delete
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td>No invoices yet!</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
