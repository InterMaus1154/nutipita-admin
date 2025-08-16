<div class="flex gap-4 items-start flex-row">
    <x-form.customer-select/>
    <x-form.form-wrapper>
        <x-form.form-label id="invoice_status" text="Status"/>
        <x-form.form-select id="invoice_status" wireModelLive="invoice_status">
            <option value=""></option>
            <option value="paid">Paid</option>
            <option value="due">Unpaid</option>
        </x-form.form-select>
    </x-form.form-wrapper>
    <flux:button wire:click="clearFilter()" class="cursor-pointer mt-8">
        <flux:icon.brush-cleaning/>
    </flux:button>
</div>
