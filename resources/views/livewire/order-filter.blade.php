@use(App\Enums\OrderStatus)
<div class="flex flex-col gap-4 ">
    <div class="flex gap-8 justify-between 2xl:justify-evenly flex-wrap sm:grid grid-cols-3 items-center">
        <div class="flex gap-4 items-center 2xl:justify-self-end">
            {{--customer filter--}}
            <x-form.customer-select />
            {{--status filter--}}
            <x-form.form-wrapper center="true">
                <x-form.form-label id="order_status" text="Status"/>
                <x-form.form-select id="order_status" wireModelLive="status">
                    <option value=""></option>
                    @foreach(OrderStatus::cases() as $orderStatus)
                        <option value="{{$orderStatus->name}}">{{ucfirst(\Illuminate\Support\Str::limit($orderStatus->value, 18, ''))}}</option>
                    @endforeach
                </x-form.form-select>
            </x-form.form-wrapper>
        </div>
        {{--quick due date filters--}}
        <div class="flex flex-col items-center flex-wrap gap-2">
            <x-form.form-label text="Due date range"/>
            <div class="flex flex-wrap gap-4">
                <flux:button wire:click="clearFilter()">
                    <flux:icon.brush-cleaning/>
                </flux:button>
                <x-form.quick-date-buttons :activePeriod="$activePeriod" :months="$months"/>
                <flux:button :variant="$daytime_only ? 'primary' : 'filled'" wire:click="toggleDaytime()">
                    <flux:icon.sun/>
                </flux:button>
            </div>
        </div>
        {{--due date inputs--}}
        <div class="flex gap-6 justify-self-end 2xl:justify-self-start flex-wrap">
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
</div>
