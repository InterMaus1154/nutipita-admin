@php use App\Models\Customer; @endphp
<x-form.form-wrapper class="sm:max-w-[210px]!">
    <x-form.form-label id="customer_id" text="Customer"/>
    @php
        // match customers to key - value
        $options = $customers->map(function (Customer $customer) {
        return[
            'key' => $customer->customer_id,
            'value' => $customer->customer_name
        ];
        })
    ->prepend(['key' => '', 'value' => ''])
    ->toArray();
    @endphp

{{--    <x-form.form-select id="customer_id" name="customer_id" wireModelLive="customer_id">--}}
{{--        <option value=""></option>--}}
{{--        @foreach($customers as $customer)--}}
{{--            <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>--}}
{{--        @endforeach--}}
{{--    </x-form.form-select>--}}
    <x-ui.select :options="$options" id="customer_id" name="customer_id" wire-model="customer_id" show-max="10"/>
</x-form.form-wrapper>
