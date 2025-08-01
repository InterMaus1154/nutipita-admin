<div class="flex flex-col gap-4 items-start">
    <x-form.form-wrapper>
        <x-form.form-label id="customer_id" text="Customer"/>
        <x-form.form-select id="customer_id" wireModelLive="customer_id">
            <option value="">---Select customer---</option>
            @foreach($customers as $customer)
                <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
            @endforeach
        </x-form.form-select>
    </x-form.form-wrapper>
    <div class="flex gap-6">
        <x-form.form-wrapper>
            <x-form.form-label id="due_from" text="Due From"/>
            <x-form.form-input type="date" id="due_from" wireModelLive="due_from" placeholder="Due From"/>
        </x-form.form-wrapper>
        <x-form.form-wrapper>
            <x-form.form-label id="due_to" text="Due To"/>
            <x-form.form-input type="date" id="due_to" wireModelLive="due_to" placeholder="Due To"/>
        </x-form.form-wrapper>
    </div>
    <h4 class="font-bold">Due date quick filter:</h4>
    <div class="flex flex-wrap gap-4">
        <flux:button.group>
            <flux:button wire:click="setYear">Year</flux:button>
            <flux:button wire:click="setMonth">Month</flux:button>
            <flux:button wire:click="setWeek">Week</flux:button>
        </flux:button.group>
        <flux:button.group>
            <flux:button wire:click="setToday">Today</flux:button>
            <flux:button wire:click="setYesterday">Yesterday</flux:button>
        </flux:button.group>
    </div>
    <x-form.form-wrapper>
        <x-form.form-label id="month" text="Month (in {{now()->year}})"/>
        <x-form.form-select id="month" wireModelLive="month">
            <option value="">---Select month---</option>
            @foreach($months as $number => $month)
                <option value="{{$number}}">{{$month}}</option>
            @endforeach
        </x-form.form-select>
    </x-form.form-wrapper>
    <x-form.form-wrapper>
        <x-form.form-label id="order_status" text="Status"/>
        <x-form.form-select id="order_status" wireModelLive="status">
            <option value="">---Select status---</option>
            @foreach(\App\Enums\OrderStatus::cases() as $orderStatus)
                <option value="{{$orderStatus->name}}">{{ucfirst($orderStatus->value)}}</option>
            @endforeach
        </x-form.form-select>
    </x-form.form-wrapper>
    <x-form.form-wrapper>
        <x-form.form-label id="hide_order" text="Hide Cancelled & Invalidated from the List"/>
        <x-form.form-input id="hide_order" type="checkbox" wireModelLive="cancelled_order_hidden" noFullWidth="true" checked/>
    </x-form.form-wrapper>
    <flux:button class="cursor-pointer" wire:click="clearFilter">Clear filter</flux:button>
</div>
