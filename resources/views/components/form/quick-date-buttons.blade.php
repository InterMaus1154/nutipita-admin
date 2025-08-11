@props(['activePeriod'])
<flux:button.group>
    <flux:button :variant="$activePeriod === 'year' ? 'primary' : 'filled'" wire:click="setYear">Year</flux:button>
    <flux:button :variant="$activePeriod === 'month' ? 'primary' : 'filled'" wire:click="setMonth">Month</flux:button>
    <flux:button :variant="$activePeriod === 'week' ? 'primary' : 'filled'" wire:click="setWeek">Week</flux:button>
    <flux:button :variant="$activePeriod === 'today' ? 'primary' : 'filled'" wire:click="setToday">Today</flux:button>
    <flux:button :variant="$activePeriod === 'yesterday' ? 'primary' : 'filled'" wire:click="setYesterday">Yesterday</flux:button>
</flux:button.group>
