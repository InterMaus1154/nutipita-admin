@use(App\Domain\Invoice\InvoiceStatus)
@props(['invoice'])
<div class="cursor-pointer">
    @php
        /**
        * @var \App\Domain\Invoice\Invoice $invoice
         */
        $bgColor = $invoice->getInvoiceStatus()->backgroundColor();
        $shadowColor = $invoice->getInvoiceStatus()->shadowColor();
        $classes = "$bgColor text-black! w-[110px]! px-2! py-2! mx-auto!";
    @endphp
    <x-form.form-wrapper>
        <x-ui.select.select wire-change="updateInvoiceStatus"
                            :wire-change-prop="$invoice->invoice_id"
                            :pre-selected-value="$invoice->getInvoiceStatus()->value"
                            inner-class="text-black text-sm! outline-0!"
                            :bg="$bgColor"
                            wrapper-class="w-[80px] sm:w-[100px] min-w-0! sm:mx-auto!"
                            wireKey="invoice-status-{{$invoice->invoice_id}}-{{$invoice->getInvoiceStatus()->value}}"
                            :shadow-color="$shadowColor"

        >
            <x-slot:options>
                @foreach(InvoiceStatus::cases() as $status)
                    <x-ui.select.option :value="$status->value" :text="$status->label()"/>
                @endforeach
            </x-slot:options>
        </x-ui.select.select>
    </x-form.form-wrapper>
</div>
