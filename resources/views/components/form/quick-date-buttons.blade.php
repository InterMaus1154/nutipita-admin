@props(['activePeriod', 'months'])
<div>
    {{--destop view--}}
    <div class="hidden sm:block">
        <flux:button.group>
            <flux:button :variant="$activePeriod === 'year' ? 'primary' : 'filled'" wire:click="setYear">Year</flux:button>
            <flux:button :variant="$activePeriod === 'month' ? 'primary' : 'filled'" wire:click="setMonth">Month</flux:button>
            <flux:button :variant="$activePeriod === 'week' ? 'primary' : 'filled'" wire:click="setWeek">Week</flux:button>
            <flux:button :variant="$activePeriod === 'today' ? 'primary' : 'filled'" wire:click="setToday">Today</flux:button>
            <flux:button :variant="$activePeriod === 'yesterday' ? 'primary' : 'filled'" wire:click="setYesterday">Yesterday
            </flux:button>
        </flux:button.group>
    </div>
    {{--mobile view--}}
    <div class="flex flex-wrap gap-4 sm:hidden">
        <flux:button.group>
            <flux:button :variant="$activePeriod === 'year' ? 'primary' : 'filled'" wire:click="setYear">Year</flux:button>
            <flux:button :variant="$activePeriod === 'month' ? 'primary' : 'filled'" wire:click="setMonth">Month</flux:button>
            <flux:button :variant="$activePeriod === 'week' ? 'primary' : 'filled'" wire:click="setWeek">Week</flux:button>
        </flux:button.group>
        <flux:button.group>
            <flux:button :variant="$activePeriod === 'today' ? 'primary' : 'filled'" wire:click="setToday">Today</flux:button>
            <flux:button :variant="$activePeriod === 'yesterday' ? 'primary' : 'filled'" wire:click="setYesterday">Yesterday
            </flux:button>
        </flux:button.group>
    </div>
</div>

