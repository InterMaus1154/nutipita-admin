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

        function getMonthsOfTheYear()
        {
            $year = now()->year;

            $months = collect();
            for($i = 1; $i<=12;$i++){
                $months->push([
                    'month' => Carbon::create($year, $i, 1)->format('F'),
                    'start_date' => Carbon::create($year, $i, 1)->startOfMonth()->toDateString(),
                    'end_date' => Carbon::create($year, $i, 1)->endOfMonth()->toDateString()
                ]);
            }

            return $months;
        }
    @endphp
    <div class="relative" x-data="{weekOpen: false, selectedWeek: null, monthOpen: false, selectedMonth: null}">
        <flux:button.group>
            <flux:button :variant="$activePeriod === 'year' ? 'primary' : 'filled'" wire:click="setYear">Y</flux:button>
            <flux:button :variant="$activePeriod === 'month' ? 'primary' : 'filled'" x-on:click="monthOpen = !monthOpen; $nextTick(()=>{
                const el = document.getElementById('current-month');
                if(el) el.scrollIntoView({ block: 'center', behavior: 'instant' });
            })">M &darr;
            </flux:button>
            <flux:button :variant="$activePeriod === 'week' ? 'primary' : 'filled'" x-on:click="weekOpen = !weekOpen; $nextTick(() => {
                    const el = document.getElementById('current-week');
                    if(el) el.scrollIntoView({ block: 'center', behavior: 'instant' });
                })">W &darr;
            </flux:button>
            <flux:button :variant="$activePeriod === 'today' ? 'primary' : 'filled'" wire:click="setToday">T
            </flux:button>
            <flux:button :variant="$activePeriod === 'yesterday' ? 'primary' : 'filled'" wire:click="setYesterday">Y
            </flux:button>
        </flux:button.group>
        {{--week selector--}}
        <div class="absolute z-[220] left-20 border-2 border-neutral-700 rounded-xl darK:bg-zinc-800/80  backdrop-blur-lg p-4 flex flex-col gap-4 w-[100px] h-[300px] overflow-scroll"
             x-show="weekOpen" x-cloak x-transition x-on:click.outside="weekOpen = false">
            @foreach(getWeeksOfTheYear() as $index => $week)
                @php
                    $today = now()->toDateString();
                    $isCurrentWeek = $today >= $week['start_date'] && $today < $week['end_date'];
                @endphp
                <div class="cursor-pointer text-center py-1 px-4 rounded-sm hover:bg-neutral-500/50"
                     x-on:click="selectedWeek = {{$index}}; weekOpen = false"
                     :class="selectedWeek === {{$index}} || (selectedWeek === null) && {{$isCurrentWeek ? 'true' : 'false'}} ? 'bg-gray-400 text-black' : '' "
                     wire:click="setWeek('{{$week['start_date']}}', '{{$week['end_date']}}')"
                     data-week-index="{{$index}}"
                     :id="selectedWeek === {{$index}} || (selectedWeek === null) && {{$isCurrentWeek ? 'true' : 'false'}} ? 'current-week' : ''">
                    {{$week['week']}}
                </div>
            @endforeach
        </div>
        {{--month selector--}}
        <div class="absolute z-[220] left-4 border-2 border-neutral-700 rounded-xl dark:bg-zinc-800/80  backdrop-blur-lg p-4 flex flex-col gap-4 w-[150px] h-[300px] overflow-scroll"
             x-show="monthOpen" x-cloak x-transition x-on:click.outside="monthOpen = false">
            @foreach(getMonthsOfTheYear() as $index => $month)
                @php
                    $today = now()->toDateString();
                    $isCurrentMonth = $today >= $month['start_date'] && $today < $month['end_date'];
                @endphp
                <div class="cursor-pointer text-center py-1 px-4 rounded-sm hover:bg-neutral-500/50"
                     x-on:click="selectedMonth = {{$index}}; monthOpen = false"
                     :class="selectedMonth === {{$index}} || (selectedMonth === null) && {{$isCurrentMonth ? 'true' : 'false'}} ? 'bg-gray-400 text-black' : '' "
                     wire:click="setMonth('{{$month['start_date']}}', '{{$month['end_date']}}')"
                     data-week-index="{{$index}}"
                     :id="selectedMonth === {{$index}} || (selectedMonth === null) && {{$isCurrentMonth ? 'true' : 'false'}} ? 'current-month' : ''">
                    {{$month['month']}}
                </div>
            @endforeach
        </div>
    </div>
</div>

