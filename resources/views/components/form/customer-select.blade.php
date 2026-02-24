<x-form.form-wrapper class="sm:w-[200px]">
    <x-form.form-label id="customer_id" text="Customer"/>
    <x-ui.select.select name="customer_id" wire-model="customer_id" wrapper-class="sm:w-full" list-class="max-h-[500px]" :has-wire="$hasWire">
        <x-slot:options>
            <x-ui.select.option value="" text="Clear"/>
            @foreach($customers as $customer)
                <x-ui.select.option value="{{$customer->customer_id}}" text="{{$customer->customer_name}}"/>
            @endforeach
        </x-slot:options>
    </x-ui.select.select>
</x-form.form-wrapper>
