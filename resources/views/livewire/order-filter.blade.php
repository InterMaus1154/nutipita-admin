<div class="flex flex-col gap-8 items-start">
    <div class="flex gap-8 items-center flex-wrap">
        {{--customer filter--}}
        <x-form.form-wrapper>
            <x-form.form-label id="customer_id" text="Customer"/>
            <x-form.form-select id="customer_id" wireModelLive="customer_id">
                <option value="">---Select customer---</option>
                @foreach($customers as $customer)
                    <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                @endforeach
            </x-form.form-select>
        </x-form.form-wrapper>
        {{--status filter--}}
        <x-form.form-wrapper>
            <x-form.form-label id="order_status" text="Status"/>
            <x-form.form-select id="order_status" wireModelLive="status">
                <option value="">---Select status---</option>
                @foreach(\App\Enums\OrderStatus::cases() as $orderStatus)
                    <option value="{{$orderStatus->name}}">{{ucfirst($orderStatus->value)}}</option>
                @endforeach
            </x-form.form-select>
        </x-form.form-wrapper>
        <div class="flex flex-col items-center flex-wrap gap-2">
            <h4 class="font-bold">Due date quick filter:</h4>
            <div class="flex flex-wrap gap-4">
                <flux:button.group>
                    <flux:button :variant="$activePeriod === 'year' ? 'primary' : 'filled'" wire:click="setYear">Year</flux:button>
                    <flux:button :variant="$activePeriod === 'month' ? 'primary' : 'filled'" wire:click="setMonth">Month</flux:button>
                    <flux:button :variant="$activePeriod === 'week' ? 'primary' : 'filled'" wire:click="setWeek">Week</flux:button>
                    <flux:button :variant="$activePeriod === 'today' ? 'primary' : 'filled'" wire:click="setToday">Today</flux:button>
                    <flux:button :variant="$activePeriod === 'yesterday' ? 'primary' : 'filled'" wire:click="setYesterday">Yesterday</flux:button>
                </flux:button.group>
                <flux:button :variant="$daytime_only ? 'primary' : 'filled'" wire:click="toggleDaytime()">
                    <flux:icon.sun />
                </flux:button>
            </div>
        </div>

    </div>
{{--    <div class="flex gap-6">--}}
{{--        <x-form.form-wrapper>--}}
{{--            <x-form.form-label id="due_from" text="Due From"/>--}}
{{--            <x-form.form-input type="date" id="due_from" wireModelLive="due_from" placeholder="Due From"/>--}}
{{--        </x-form.form-wrapper>--}}
{{--        <x-form.form-wrapper>--}}
{{--            <x-form.form-label id="due_to" text="Due To"/>--}}
{{--            <x-form.form-input type="date" id="due_to" wireModelLive="due_to" placeholder="Due To"/>--}}
{{--        </x-form.form-wrapper>--}}
{{--    </div>--}}

{{--    <x-form.form-wrapper>--}}
{{--        <x-form.form-label id="month" text="Month (in {{now()->year}})"/>--}}
{{--        <x-form.form-select id="month" wireModelLive="month">--}}
{{--            <option value="">---Select month---</option>--}}
{{--            @foreach($months as $number => $month)--}}
{{--                <option value="{{$number}}">{{$month}}</option>--}}
{{--            @endforeach--}}
{{--        </x-form.form-select>--}}
{{--    </x-form.form-wrapper>--}}

{{--    <x-form.form-wrapper>--}}
{{--        <x-form.form-label id="hide_order">--}}
{{--            <flux:badge color="red">--}}
{{--                Hide Cancelled & Invalidated from the List--}}
{{--            </flux:badge>--}}
{{--        </x-form.form-label>--}}
{{--        <x-form.form-input id="hide_order" type="checkbox" wireModelLive="cancelled_order_hidden" noFullWidth="true" checked/>--}}
{{--    </x-form.form-wrapper>--}}
    <flux:button class="cursor-pointer" wire:click="clearFilter">Clear filter</flux:button>
</div>
