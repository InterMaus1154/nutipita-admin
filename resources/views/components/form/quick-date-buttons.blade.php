@props(['activePeriod', 'months', 'class' => ''])
@use(Carbon\Carbon)
@use(Carbon\WeekDay)
<div class="{{$class}}">
    @php
        function getWeeksOfTheYear()
        {
            $year = now()->year;

            $start = Carbon::create($year, 1, 1)->startOfWeek(WeekDay::Sunday);
            $end = Carbon::create($year, 12, 31)->endOfWeek(WeekDay::Saturday);

            $weeks = collect();

            $weekNumber = 1;

            for ($date = $start->copy(); $date->lte($end); $date->addWeek()) {
                $weekStart = $date->copy();
                $weekEnd = $date->copy()->endOfWeek(WeekDay::Saturday);

                $weeks->push([
                    'week' => $weekNumber,
                    'start_date' => $weekStart->toDateString(),
                    'end_date'  => $weekEnd->toDateString()
                ]);
                $weekNumber++;
            }
            return $weeks;
        }
    @endphp

    <div class="relative" x-data="{open = true}">
        <flux:button.group>
            <flux:button :variant="$activePeriod === 'year' ? 'primary' : 'filled'" wire:click="setYear">Y</flux:button>
            <flux:button :variant="$activePeriod === 'month' ? 'primary' : 'filled'" wire:click="setMonth">M</flux:button>
            <flux:button :variant="$activePeriod === 'week' ? 'primary' : 'filled'" x-on:click="open = true" wire:click="setWeek">W</flux:button>
            <flux:button :variant="$activePeriod === 'today' ? 'primary' : 'filled'" wire:click="setToday">T</flux:button>
            <flux:button :variant="$activePeriod === 'yesterday' ? 'primary' : 'filled'" wire:click="setYesterday">Y
            </flux:button>
        </flux:button.group>
{{--        <div class="absolute border-2 border-neutral-700 rounded-xl bg-zinc-800 p-4 flex flex-col gap-4 w-[100px] h-[300px] overflow-scroll" x-show="open" x-cloak x-transition x-on:click.outside="open = false">--}}
{{--            @foreach(getWeeksOfTheYear() as $week)--}}
{{--                <div class="text-center py-1 px-4 rounded-sm hover:bg-neutral-500/50">--}}
{{--                    {{$week['week']}}--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        </div>--}}
    </div>
</div>

