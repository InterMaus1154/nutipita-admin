@use(App\Enums\OrderStatus)
<div class="flex flex-col gap-4 ">
    <div class="flex gap-8 justify-center sm:justify-between 2xl:justify-evenly flex-wrap sm:grid grid-cols-3 items-center">
        <div class="flex gap-4 items-center 2xl:justify-self-end">
            {{--customer filter--}}
            <x-form.customer-select/>
            {{--status filter--}}
            <x-form.form-wrapper>
                <x-form.form-label id="order_status" text="Status"/>
                <x-form.form-select id="order_status" wireModelLive="status">
                    <option value=""></option>
                    @foreach(OrderStatus::cases() as $orderStatus)
                        <option
                            value="{{$orderStatus->name}}">{{ucfirst(\Illuminate\Support\Str::limit($orderStatus->value, 18, ''))}}</option>
                    @endforeach
                </x-form.form-select>
            </x-form.form-wrapper>
        </div>
        <div class="flex flex-col gap-2 mt-8 sm:flex-row sm:justify-center sm:items-center">
            <flux:button wire:click="clearFilter()" class="sm:order-1 hidden! sm:block!">
                <flux:icon.brush-cleaning/>
            </flux:button>
            <x-form.quick-date-buttons
                :activePeriod="$activePeriod"
                :months="$months"
                class="w-full sm:w-auto order-1 sm:order-2"
            />
            <flux:button
                wire:click="toggleDaytime()"
                class="hidden! sm:block! sm:order-3 {{ $daytime_only ? 'bg-yellow-300 text-black' : '' }}"
            >
                <flux:icon.sun />
            </flux:button>

            {{--mobile only bullshit--}}
            <div class="flex gap-2 justify-between sm:hidden order-2 sm:order-none">
                <flux:button wire:click="clearFilter()" class="order-2 sm:order-1">
                    <flux:icon.brush-cleaning/>
                </flux:button>
                <flux:button
                    wire:click="toggleDaytime()"
                    class="{{ $daytime_only ? 'bg-yellow-300 text-black' : '' }}"
                >
                    <flux:icon.sun />
                </flux:button>
            </div>
        </div>
        {{--due date inputs--}}
        <div class="flex gap-6 justify-self-end 2xl:justify-self-start flex-wrap">
            <x-form.form-wrapper>
                <x-form.form-label id="due_from" text="Due From"/>
                <x-form.form-input type="date" id="due_from" wireModelLive="due_from" placeholder="Due From"/>
            </x-form.form-wrapper>
            <x-form.form-wrapper>
                <x-form.form-label id="due_to" text="Due To"/>
                <x-form.form-input type="date" id="due_to" wireModelLive="due_to" placeholder="Due To"/>
            </x-form.form-wrapper>
        </div>
    </div>
</div>
