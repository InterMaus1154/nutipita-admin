<div class="flex flex-col gap-4">
    <x-error/>
    <x-success/>
    @if(session()->has('invoice'))
        <div class="flex gap-4">
            <flux:link href="{{route('invoices.download', ['invoice' => session()->get('invoice')])}}">
                Download invoice
            </flux:link>
            <flux:link href="{{route('invoices.view-inline', ['invoice' => session()->get('invoice')])}}"
            >View PDF
            </flux:link>
        </div>
    @endif
    <form method="POST" wire:submit="submit" class="flex flex-col gap-4">
        @csrf
        <div class="max-w-sm">
            <label for="customer_id" class="block text-sm font-medium mb-2 dark:text-white">Customer</label>
            <select name="customer_id" id="customer_id" wire:model.live="customer_id" class="py-3 px-4 pe-9 block w-full bg-gray-100 border-transparent rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-700 dark:border-transparent dark:text-neutral-400 dark:focus:ring-neutral-600">
                <option value="">---Select a customer---</option>
                @foreach($customers as $customer)
                    <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                @endforeach
            </select>
        </div>
        <h3 class="font-bold">Order Info</h3>
        <div class="flex gap-6">
            <div class="max-w-sm">
                <label for="due_from" class="block text-sm font-medium mb-2 dark:text-white">Order due from:</label>
                <input type="date" id="due_from" name="due_from" wire:model.live="due_from" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="max-w-sm">
                <label for="due_to" class="block text-sm font-medium mb-2 dark:text-white">Order due to:</label>
                <input type="date" id="due_to" name="due_to" wire:model.live="due_to" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
        </div>
        <div class="flex flex-wrap gap-4">
            <flux:button.group>
                <flux:button wire:click="setYear">Year</flux:button>
                <flux:button wire:click="setMonth">Month</flux:button>
                <flux:button wire:click="setWeek">Week</flux:button>
            </flux:button.group>
            <flux:button.group>
                <flux:button wire:click="setToday">Today</flux:button>
                <flux:button wire:click="setYesterday">Yesterday</flux:button>
            </flux:button.group>
        </div>
        <x-form.form-wrapper>
            <x-form.form-label id="order_status" text="Order Status"/>
            <x-form.form-select id="order_status" wireModelLive="order_status">
                <option value="">---Select status---</option>
                @foreach(\App\Enums\OrderStatus::cases() as $orderStatus)
                    <option value="{{$orderStatus->name}}">{{ucfirst($orderStatus->value)}}</option>
                @endforeach
            </x-form.form-select>
        </x-form.form-wrapper>
        <h3 class="font-bold">Invoice Info</h3>
        <div class="flex gap-6">
            <div class="max-w-sm">
                <label for="invoice_issue_date" class="block text-sm font-medium mb-2 dark:text-white">Invoice Issue Date</label>
                <input type="date" id="invoice_issue_date" name="invoice_issue_date"
                       value="{{now()->toDateString()}}" wire:model="invoice_issue_date" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
            <div class="max-w-sm">
                <label for="invoice_due_date" class="block text-sm font-medium mb-2 dark:text-white">Invoice Due Date</label>
                <input type="date" id="invoice_due_date" name="invoice_due_date"
                       value="{{now()->addDay()->toDateString()}}" wire:model="invoice_due_date" class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
            </div>
        </div>
        <x-form.form-wrapper>
            <x-form.form-label id="invoice_number" text="Invoice Number"/>
            <x-form.form-input id="invoice_number" type="string" wireModel="invoice_number"/>
        </x-form.form-wrapper>
        <flux:button variant="primary" type="submit">Generate Invoice</flux:button>
    </form>
    <livewire:order-list withSummaryPdf="true"/>
</div>
