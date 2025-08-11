<div class="flex gap-4">
    <flux:button :variant="($is_nighttime && $is_daytime) ? 'primary' : 'filled'" wire:click="setBoth">
        <flux:icon.sun class="!inline"/>
        <flux:icon.moon class="!inline"/>
    </flux:button>
    <flux:button :variant="($is_nighttime && !$is_daytime) ? 'primary' : 'filled'" wire:click="setNighttime">
        <flux:icon.moon class="!inline"/>
    </flux:button>
    <flux:button :variant="($is_daytime && !$is_nighttime) ? 'primary' : 'filled'" wire:click="setDaytime">
        <flux:icon.sun class="!inline"/>
    </flux:button>
</div>
