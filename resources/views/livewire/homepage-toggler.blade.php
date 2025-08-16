<div class="flex gap-4 items-center mx-auto">
    <flux:button :class="($is_daytime && !$is_nighttime) ? 'bg-yellow-300!' : ''"  wire:click="setDaytime">
        <flux:icon.sun :class="($is_daytime && !$is_nighttime) ? 'text-black' : ''"/>
    </flux:button>
    <flux:button :variant="($is_nighttime && $is_daytime) ? 'primary' : 'filled'" wire:click="setBoth" class="cursor-pointer">
        <flux:icon.sun class="!inline"/>
        <flux:icon.moon class="!inline"/>
    </flux:button>
    <flux:button :class="($is_nighttime && !$is_daytime) ? 'bg-violet-600!' : ''" wire:click="setNighttime">
        <flux:icon.moon :class="($is_nighttime && !$is_daytime) ? 'text-white!' : ''"/>
    </flux:button>

</div>
