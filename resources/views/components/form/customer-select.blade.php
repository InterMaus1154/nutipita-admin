@php use App\Models\Customer; @endphp
<x-form.form-wrapper class="sm:max-w-[250px]!">
    <x-form.form-label id="customer_id" text="Customer"/>
    <x-form.form-select id="customer_id" name="customer_id" wireModelLive="customer_id">
        <option value=""></option>
        @foreach($customers as $customer)
            <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
        @endforeach
    </x-form.form-select>
</x-form.form-wrapper>
