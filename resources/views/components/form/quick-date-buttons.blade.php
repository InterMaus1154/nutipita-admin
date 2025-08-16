@props(['activePeriod', 'months'])
<div>
    <div>
        <flux:button.group>
            <flux:button :variant="$activePeriod === 'year' ? 'primary' : 'filled'" wire:click="setYear">Y</flux:button>
            <flux:button :variant="$activePeriod === 'month' ? 'primary' : 'filled'" wire:click="setMonth">M</flux:button>
            <flux:button :variant="$activePeriod === 'week' ? 'primary' : 'filled'" wire:click="setWeek">W</flux:button>
            <flux:button :variant="$activePeriod === 'today' ? 'primary' : 'filled'" wire:click="setToday">T</flux:button>
            <flux:button :variant="$activePeriod === 'yesterday' ? 'primary' : 'filled'" wire:click="setYesterday">Y
            </flux:button>
        </flux:button.group>
    </div>
</div>

