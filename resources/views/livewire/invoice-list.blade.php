<div class="space-y-4">
    <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                        <thead>
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Invoice#
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Customer
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Issue Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Due Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Orders From
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Orders To
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Invoice Total - £
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-md font-medium text-gray-500 uppercase dark:text-neutral-500">
                                Actions
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoices as $invoice)
                            <tr class="odd:bg-white even:bg-gray-100 hover:bg-gray-100 dark:odd:bg-neutral-800 dark:even:bg-neutral-700 dark:hover:bg-neutral-700 text-center">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    INV-{{$invoice->invoice_number}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    <flux:link
                                        href="{{route('customers.show', ['customer' => $invoice->customer])}}">{{$invoice->customer->customer_name}}</flux:link>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{dayDate($invoice->invoice_issue_date)}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{dayDate($invoice->invoice_due_date)}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200 italic">{{ucfirst($invoice->invoice_status)}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{$invoice->invoice_from ? dayDate($invoice->invoice_from) : "-"}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{$invoice->invoice_to ? dayDate($invoice->invoice_to) : "-"}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">£{{$invoice->invoice_total}}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200 space-x-1.5">
                                    <flux:link href="{{route('invoices.view-inline', compact('invoice'))}}">View PDF</flux:link>
                                    <flux:link href="{{route('invoices.download', compact('invoice'))}}">Download</flux:link>
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
                                       wire:confirm="Are you sure to delete this invoice?"
                                    >Delete
                                    </flux:link>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
