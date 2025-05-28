<div class="filter-wrapper">
    <div class="input-wrapper">
        <label for="customer_id">Customer</label>
        <select id="customer_id" wire:model.live="customer_id">
            <option value="">---Select customer---</option>
            @foreach($customers as $customer)
                <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
            @endforeach
        </select>
    </div>
</div>
