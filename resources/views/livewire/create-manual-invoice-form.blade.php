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
        <div class="flex flex-wrap gap-4 sm:grid grid-cols-3 items-center">
            <div class="flex gap-4">
                {{--customer--}}
                <x-form.form-wrapper center="true">
                    <label for="customer_id" class="block text-sm font-medium mb-2 dark:text-white">Customer*</label>
                    <select name="customer_id" id="customer_id" wire:model.live="customer_id"
                            class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                        <option value=""></option>
                        @foreach($customers as $customer)
                            <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                        @endforeach
                    </select>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="order_status" text="Order Status"/>
                    <x-form.form-select id="order_status" wireModelLive="order_status">
                        <option value="">---Select status---</option>
                        @foreach(\App\Enums\OrderStatus::cases() as $orderStatus)
                            <option value="{{$orderStatus->name}}">{{ucfirst($orderStatus->value)}}</option>
                        @endforeach
                    </x-form.form-select>
                </x-form.form-wrapper>
            </div>
            {{--quick date buttons--}}
            <div class="flex flex-col items-center flex-wrap gap-2">
                <x-form.form-label text="Due date range"/>
                <div class="flex flex-wrap gap-4">
                    <x-form.quick-date-buttons :activePeriod="$activePeriod" :months="$months"/>
                </div>
            </div>
            {{--order date from/to--}}
            <div class="flex gap-6 justify-self-end">
                <x-form.form-wrapper center="true">
                    <label for="due_from" class="block text-sm font-medium mb-2 dark:text-white">Order due from:</label>
                    <input type="date" id="due_from" name="due_from" wire:model.live="due_from"
                           class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                </x-form.form-wrapper>
                <x-form.form-wrapper center="true">
                    <label for="due_to" class="block text-sm font-medium mb-2 dark:text-white">Order due to:</label>
                    <input type="date" id="due_to" name="due_to" wire:model.live="due_to"
                           class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                </x-form.form-wrapper>
            </div>
        </div>
        <div class="flex justify-between">
            <div class="flex gap-6">
                <x-form.form-wrapper center="true">
                    <label for="invoice_issue_date" class="block text-sm font-medium mb-2 dark:text-white">Invoice Issue
                        Date*
                    </label>
                    <input type="date" id="invoice_issue_date" name="invoice_issue_date"
                           value="{{now()->toDateString()}}" wire:model="invoice_issue_date"
                           class="py-2.5 sm:py-3 px-4 block border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                </x-form.form-wrapper>
                <x-form.form-wrapper center="true">
                    <label for="invoice_due_date" class="block text-sm font-medium mb-2 dark:text-white">Invoice Due
                        Date*
                    </label>
                    <input type="date" id="invoice_due_date" name="invoice_due_date"
                           value="{{now()->addDay()->toDateString()}}" wire:model="invoice_due_date"
                           class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                </x-form.form-wrapper>
            </div>
            <x-form.form-wrapper center="true">
                <label for="invoice_number" class="block text-sm font-medium mb-2 dark:text-white">Invoice Number*
                </label>
                <input type="text" name="invoice_number" wire:model="invoice_number"
                       class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </x-form.form-wrapper>
        </div>
        @if(!empty($customer_id))
            @foreach($products as $product)
                @php($product->setCurrentCustomer($customer_id))
                {{--only render products, that has prices set for a customer--}}
                @if($product->price > 0)
                    <div class="grid grid-cols-2 grid-rows-2 max-w-sm text-center">
                        <label
                                for="invoiceProducts[{{$product->product_id}}]"
                                class="block text-sm font-medium mb-2 dark:text-white text-center self-center col-span-2 row-span-1">
                            {{$product->product_name}} {{$product->product_weight_g}}g
                        </label>
                        <input type="number" placeholder="Quantity" value="0"
                               id="invoiceProducts[{{$product->product_id}}]"
                               name="invoiceProducts[{{$product->product_id}}]"
                               wire:model="invoiceProducts.{{$product->product_id}}"
                               class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                        <flux:badge class="self-center justify-self-start">@unitPriceFormat($product->price)</flux:badge>
                    </div>
                @endif
            @endforeach
        @endif
        <flux:button variant="primary" type="submit">Generate Invoice</flux:button>
    </form>
    <livewire:order-list :summaryVisibleByDefault="true" :withSummaryPdf="true"/>
</div>
