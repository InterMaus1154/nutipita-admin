<div class="flex flex-col gap-4 items-start">
    <x-form.form-wrapper>
        <x-form.form-label id="customer_id" text="Customer"/>
        <x-form.form-select id="customer_id" wireModelLive="customer_id">
            <option value="">---Select a customer---</option>
            @foreach($customers as $customer)
                <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
            @endforeach
        </x-form.form-select>
    </x-form.form-wrapper>
    <x-form.form-wrapper>
        <x-form.form-label id="invoice_status" text="Payment Status"/>
        <x-form.form-select id="invoice_status" wireModelLive="invoice_status">
            <option value="">---Select a payment status---</option>
            <option value="paid">Paid</option>
            <option value="due">Unpaid</option>
        </x-form.form-select>
    </x-form.form-wrapper>
    <flux:button wire:click="clearFilter()" class="cursor-pointer">Clear Filter</flux:button>
</div>
