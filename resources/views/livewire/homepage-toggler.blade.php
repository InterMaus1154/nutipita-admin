<div class="flex gap-4 items-center">
    <flux:button :variant="($is_nighttime && $is_daytime) ? 'primary' : 'filled'" wire:click="setBoth" class="cursor-pointer">
        <flux:icon.sun class="!inline"/>
        <flux:icon.moon class="!inline"/>
    </flux:button>
    <flux:button :variant="($is_nighttime && !$is_daytime) ? 'primary' : 'filled'" wire:click="setNighttime" class="cursor-pointer">
        <flux:icon.moon class="!inline"/>
    </flux:button>
    <flux:button :variant="($is_daytime && !$is_nighttime) ? 'primary' : 'filled'" wire:click="setDaytime" class="cursor-pointer">
        <flux:icon.sun class="!inline"/>
    </flux:button>
    @if($is_daytime && $is_nighttime)
        <flux:link class="cursor-not-allowed" title="Cant add order if no specific shift is selected">
            <flux:icon.no-symbol class="size-8"/>
        </flux:link>
    @elseif($is_daytime)
        <flux:link href="{{route('orders.create',  [
    'order_due_at' => now()->toDateString(),
    'is_daytime' => true
])}}" class="cursor-pointer" title="Add new order">
            <flux:icon.plus-circle class="size-8"/>
        </flux:link>
    @elseif($is_nighttime)
        <flux:link href="{{route('orders.create')}}" class="cursor-pointer" title="Add new order">
            <flux:icon.plus-circle class="size-8"/>
        </flux:link>
    @endif
</div>
