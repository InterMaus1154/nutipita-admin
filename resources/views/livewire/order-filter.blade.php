@use(App\Enums\OrderStatus)
<div class="flex flex-col gap-4 ">
    <div class="flex gap-8 justify-between flex-wrap sm:grid grid-cols-3 items-center">
        <div class="flex gap-4 items-center">
            {{--customer filter--}}
            <x-form.form-wrapper center="true">
                <x-form.form-label id="customer_id" text="Customer"/>
                <x-form.form-select id="customer_id" wireModelLive="customer_id">
                    <option value=""></option>
                    @foreach($customers as $customer)
                        <option value="{{$customer->customer_id}}">{{$customer->customer_name}}</option>
                    @endforeach
                </x-form.form-select>
            </x-form.form-wrapper>
            {{--status filter--}}
            <x-form.form-wrapper center="true">
                <x-form.form-label id="order_status" text="Status"/>
                <x-form.form-select id="order_status" wireModelLive="status">
                    <option value=""></option>
                    @foreach(OrderStatus::cases() as $orderStatus)
                        <option value="{{$orderStatus->name}}">{{ucfirst($orderStatus->value)}}</option>
                    @endforeach
                </x-form.form-select>
            </x-form.form-wrapper>
            <div class="flex flex-col gap-8 items-center">
                <div></div>
                <flux:button wire:click="clearFilter()">
                    <flux:icon.brush-cleaning />
                </flux:button>
            </div>
        </div>
        {{--quick due date filters--}}
        <div class="flex flex-col items-center flex-wrap gap-2">
            <x-form.form-label text="Due date range"/>
            <div class="flex flex-wrap gap-4">
                <x-form.quick-date-buttons :activePeriod="$activePeriod" :months="$months"/>
                <flux:button :variant="$daytime_only ? 'primary' : 'filled'" wire:click="toggleDaytime()">
                    <flux:icon.sun />
                </flux:button>
            </div>
        </div>
        {{--due date inputs--}}
        <div class="flex gap-6 justify-self-end">
            <x-form.form-wrapper center="true">
                <x-form.form-label id="month" text="Month"/>
                <x-form.form-select id="month" wireModelLive="month">
                    <option value=""></option>
                    @foreach($months as $number => $month)
                        <option value="{{$number}}">{{$month}}</option>
                    @endforeach
                </x-form.form-select>
            </x-form.form-wrapper>
            <x-form.form-wrapper center="true">
                <x-form.form-label id="due_from" text="Due From"/>
                <x-form.form-input type="date" id="due_from" wireModelLive="due_from" placeholder="Due From"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper center="true">
                <x-form.form-label id="due_to" text="Due To"/>
                <x-form.form-input type="date" id="due_to" wireModelLive="due_to" placeholder="Due To"/>
            </x-form.form-wrapper>
        </div>
    </div>




{{--    <x-form.form-wrapper>--}}
{{--        <x-form.form-label id="hide_order">--}}
{{--            <flux:badge color="red">--}}
{{--                Hide Cancelled & Invalidated from the List--}}
{{--            </flux:badge>--}}
{{--        </x-form.form-label>--}}
{{--        <x-form.form-input id="hide_order" type="checkbox" wireModelLive="cancelled_order_hidden" noFullWidth="true" checked/>--}}
{{--    </x-form.form-wrapper>--}}
{{--    <flux:button class="cursor-pointer self-start" wire:click="clearFilter">Clear filter</flux:button>--}}
</div>
