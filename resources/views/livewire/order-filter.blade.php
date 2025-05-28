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
    <div style="display: flex; gap: 1rem;">
        <div class="input-wrapper">
            <label for="due_from">Due from:</label>
            <input type="date" id="due_from" wire:model.live="due_from">
        </div>
        <div class="input-wrapper">
            <label for="due_to">Due to:</label>
            <input type="date" id="due_to" wire:model.live="due_to">
        </div>
    </div>
    <button wire:click="clearFilter">Clear filter</button>
</div>
