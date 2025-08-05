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
</div>
