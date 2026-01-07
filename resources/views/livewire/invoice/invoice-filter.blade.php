<div class="flex gap-4 items-center mx-auto flex-wrap">
    <x-form.customer-select has-wire="true"/>
    <div class="flex gap-6 justify-self-end 2xl:justify-self-start flex-wrap">
        <x-form.form-wrapper>
            <x-form.form-label id="invoice_from" text="From"/>
            <x-form.form-input type="date" id="invoice_from" wireModelLive="invoice_from" class="min-w-[150px]"/>
        </x-form.form-wrapper>
        <x-form.form-wrapper>
            <x-form.form-label id="invoice_to" text="To"/>
            <x-form.form-input type="date" id="invoice_to" wireModelLive="invoice_to" class="min-w-[150px]"/>
        </x-form.form-wrapper>
    </div>
</div>
