<div class="flex flex-col gap-4">
    <x-error/>
    <x-success/>
    @if(session()->has('invoice'))
        <div class="flex gap-4">
            <flux:link href="{{route('invoices.download', ['invoice' => session()->get('invoice')])}}">
                Download invoice
            </flux:link>
            <flux:link href="{{route('invoices.view-inline', ['invoice' => session()->get('invoice')])}}">View
            </flux:link>
        </div>
    @endif
    <form method="POST" wire:submit="save" class="flex flex-col gap-4">
        @csrf
        <h3 class="font-bold">Invoice Info</h3>
        <div class="max-w-sm">
            <label for="customer_id" class="block text-sm font-medium mb-2 dark:text-white">Customer*</label>
            <select name="customer_id" id="customer_id" wire:model.live="customer_id"
                    class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                <option value="">---Select a customer---</option>
                @foreach($customers as $customer)
                    <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="max-w-sm">
            <label for="invoice_number" class="block text-sm font-medium mb-2 dark:text-white">Invoice Number*</label>
            <input type="text" name="invoice_number" wire:model="invoice_number"
                   class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
        </div>
        <div class="flex gap-6">
            <div class="max-w-sm">
                <label for="invoice_issue_date" class="block text-sm font-medium mb-2 dark:text-white">Invoice Issue
                    Date*
                </label>
                <input type="date" id="invoice_issue_date" name="invoice_issue_date"
                       value="{{now()->toDateString()}}" wire:model="invoice_issue_date"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="max-w-sm">
                <label for="invoice_due_date" class="block text-sm font-medium mb-2 dark:text-white">Invoice Due
                    Date*
                </label>
                <input type="date" id="invoice_due_date" name="invoice_due_date"
                       value="{{now()->addDay()->toDateString()}}" wire:model="invoice_due_date"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
        </div>
        <h3 class="font-bold">Order Info</h3>
        <div class="flex gap-6">
            <div class="max-w-sm">
                <label for="due_from" class="block text-sm font-medium mb-2 dark:text-white">Order due from:</label>
                <input type="date" id="due_from" name="due_from" wire:model.live="due_from"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="max-w-sm">
                <label for="due_to" class="block text-sm font-medium mb-2 dark:text-white">Order due to:</label>
                <input type="date" id="due_to" name="due_to" wire:model.live="due_to"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
        </div>
        @if($orders->isNotEmpty())
            @include('livewire.order-list')
            <h3 class="font-bold">Order Total Info</h3>
            <div class="flex gap-4 flex-wrap">
                <div
                    class="flex flex-col gap-2 bg-white border border-gray-200 shadow-2xs rounded-xl p-4 md:p-5 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white text-center items-center text-xl">
                    <span>Total Pitas</span>
                    <span>{{$totalPita}}</span>
                </div>
                @foreach($productTotals as $productName => $productTotal)
                    <div
                        class="flex flex-col gap-2 bg-white border border-gray-200 shadow-2xs rounded-xl p-4 md:p-5 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white text-center items-center text-xl">
                        <span>{{$productName}}</span>
                        <span>{{$productTotal}}</span>
                    </div>
                @endforeach
            </div>
        @endif
        <h3 class="font-bold">Product Info</h3>
        @if(!empty($customer_id))
            @foreach($products as $product)
                @php($product->setCurrentCustomer($customer_id))
                <div class="max-w-sm">
                    <label
                        for="invoiceProducts[{{$product->product_id}}]"
                        class="block text-sm font-medium mb-2 dark:text-white">{{$product->product_name}} {{$product->price}}</label>
                    <input type="number" placeholder="Quantity" value="0"
                           id="invoiceProducts[{{$product->product_id}}]"
                           name="invoiceProducts[{{$product->product_id}}]"
                           wire:model="invoiceProducts.{{$product->product_id}}"
                           class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                </div>
            @endforeach
        @else
            <em>Select a customer first</em>
        @endif
        <flux:button variant="primary" type="submit">Generate Invoice</flux:button>
    </form>
</div>
