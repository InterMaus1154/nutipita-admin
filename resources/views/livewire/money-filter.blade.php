<div>
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
        <div style="display: flex; gap: 1rem;">
            <button wire:click="setYear">This Year</button>
            <button wire:click="setMonth">This Month</button>
            <button wire:click="setWeek">This Week</button>
            <button wire:click="setToday">Today</button>
        </div>
    </div>
    <div class="box-wrapper">
        <div class="box">
            <span>Total Orders</span>
            <span>{{$orderCount}}</span>
        </div>
        <div class="box">
            <span>Total Income</span>
            <span>£{{$totalIncome}}</span>
        </div>
    </div>
</div>
