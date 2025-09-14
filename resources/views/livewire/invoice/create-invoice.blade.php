@use(App\Enums\OrderStatus)
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
    <form method="POST" wire:submit="save" class="flex flex-col gap-4 m-0!">
        @csrf
        <div class="flex flex-wrap gap-4 sm:grid grid-cols-3 items-center">
            <div class="flex gap-4">
                {{--customer--}}
                <x-form.customer-select has-wire="true"/>
                <x-form.form-wrapper>
                    <x-form.form-label id="order_status" text="Order Status"/>
                    <x-ui.select.select wire-model="order_status" wrapper-class="w-[100px]" has-wire>
                        <x-slot:options>
                            <x-ui.select.option value="" text="Clear"/>
                            @foreach(OrderStatus::cases() as $orderStatus)
                                <x-ui.select.option value="{{$orderStatus->name}}" text="{{ucfirst($orderStatus->value)}}"/>
                            @endforeach
                        </x-slot:options>
                    </x-ui.select.select>
                </x-form.form-wrapper>
                {{--auto/manual toggle--}}
                <flux:button :variant="$formMode === 'manual' ? 'primary': 'filled'" class="self-center mt-7" wire:click="toggleMode()">
                    <flux:icon.hand/>
                </flux:button>
            </div>
            {{--quick date buttons--}}
            <div class="flex flex-col items-center flex-wrap gap-2">
                <x-form.form-label text="Due date range"/>
                <div class="flex flex-wrap gap-4">
                    <x-form.quick-date-buttons :activePeriod="$activePeriod" :months="$months"/>
                </div>
            </div>
            <div class="flex gap-6 justify-self-end">
                <x-form.form-wrapper>
                    <x-form.form-label id="invoice_issue_date" text="Invoice Issue Date"/>
                    <x-form.form-input type="date" id="invoice_issue_date" wireModel="invoice_issue_date"
                                       value="{{now()->toDateString()}}"/>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="invoice_due_date" text="Invoice Due Date"/>
                    <x-form.form-input type="date" id="invoice_due_date" wireModel="invoice_due_date"
                                       value="{{now()->addDay()->toDateString()}}"/>
                </x-form.form-wrapper>
            </div>
        </div>
        <div class="flex justify-between flex-wrap gap-4">
            {{--order date from/to--}}
            <div class="flex gap-6 justify-self-end">
                <x-form.form-wrapper>
                    <x-form.form-label id="due_from" text="Due From"/>
                    <x-form.form-input type="date" id="due_from" wireModelLive="due_from"/>
                </x-form.form-wrapper>
                <x-form.form-wrapper>
                    <x-form.form-label id="due_to" text="Due To"/>
                    <x-form.form-input type="date" id="due_to" wireModelLive="due_to"/>
                </x-form.form-wrapper>
            </div>
            <x-form.form-wrapper>
                <x-form.form-label id="invoice_number" text="Invoice Number"/>
                <x-form.form-input id="invoice_number" name="invoice_number" wireModel="invoice_number" class="max-w-[100px]!"/>
            </x-form.form-wrapper>
        </div>
        <div class="flex gap-4">
            @if(!empty($customer_id) && $formMode === "manual")
                @foreach($products as $product)
                    @php
                        $product->setCurrentCustomer($customer_id)
                    @endphp
                    {{--only render products, that has prices set for a customer--}}
                    @if($product->price > 0)
                        <x-form.form-wrapper>
                            <x-form.form-label id="invoiceProducts[{{$product->product_id}}]">
                                {{$product->product_name}} {{$product->product_weight_g}}g
                                - @unitPriceFormat($product->price)
                            </x-form.form-label>
                            <x-form.form-input type="number" placeholder="Quantity"
                                               id="invoiceProducts[{{$product->product_id}}]"
                                               name="invoiceProducts[{{$product->product_id}}]"
                                               wireModel="invoiceProducts.{{$product->product_id}}"
                            />
                        </x-form.form-wrapper>
                    @endif
                @endforeach
            @endif
        </div>

        <flux:button variant="primary" type="submit" class="sm:w-[50%] sm:mx-auto">Generate Invoice</flux:button>
    </form>
    @php
        // set week as default
            $filters = [
                'due_from' => now()->startOfWeek(\Carbon\WeekDay::Sunday)->format('Y-m-d'),
                'due_to' => now()->endOfWeek(\Carbon\WeekDay::Saturday)->format('Y-m-d')
                ];
    @endphp
    <livewire:order-list :summaryVisibleByDefault="true" :withSummaryPdf="true" :prop-filters="$filters"/>
</div>
