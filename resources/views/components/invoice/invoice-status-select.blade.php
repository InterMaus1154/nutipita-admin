@use(App\Enums\InvoiceStatus)
@props(['invoice'])
<div class="cursor-pointer">
    @php
        // match color
        if($invoice->invoice_status === InvoiceStatus::paid->name){
            $bgColor = "bg-green-500!";
            $shadowColor = "oklch(72.3% 0.219 149.579)";
        }else if($invoice->invoice_status === InvoiceStatus::cancelled->name){
            $bgColor = "bg-orange-400!";
            $shadowColor = "oklch(75% 0.183 55.934)";
        }else if($invoice->invoice_status === InvoiceStatus::due->name){
            $bgColor = "bg-red-500!";
            $shadowColor = "oklch(63.7% 0.237 25.331)";
        }
        $classes = "$bgColor text-black! w-[110px]! px-2! py-2! mx-auto!";
    @endphp
    <x-form.form-wrapper>
        <x-ui.select.select wire-change="updateInvoiceStatus"
                            :wire-change-prop="$invoice->invoice_id"
                            :pre-selected-value="$invoice->invoice_status"
                            inner-class="text-black text-sm! outline-0!"
                            :bg="$bgColor"
                            wrapper-class="w-[80px] sm:w-[100px] min-w-0! sm:mx-auto!"
                            wireKey="invoice-status-{{$invoice->invoice_id}}-{{$invoice->invoice_status}}"
                            :shadow-color="$shadowColor"

        >
            <x-slot:options>
                @foreach(InvoiceStatus::cases() as $status)
                    <x-ui.select.option :value="$status->name" :text="$status->value"/>
                @endforeach
            </x-slot:options>
        </x-ui.select.select>
    </x-form.form-wrapper>
</div>
