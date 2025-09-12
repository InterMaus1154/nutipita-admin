<x-flux-layout>
    <x-page-section>
        <x-page-heading title="Test Custom Select"/>
        <x-ui.select.select name="customer_id" placeholder="Select customer" width="w-[200px]">
            <x-slot:options>
                <x-ui.select.option text="Clear" value=""/>
                @foreach(\App\Models\Customer::all() as $customer)
                    <x-ui.select.option text="{{$customer->customer_name}}" value="{{$customer->customer_id}}"/>
                @endforeach
            </x-slot:options>
        </x-ui.select.select>
    </x-page-section>
</x-flux-layout>
