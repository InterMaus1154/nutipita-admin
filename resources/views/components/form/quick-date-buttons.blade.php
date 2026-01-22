@props(['activePeriod', 'months', 'class' => ''])
@use(App\Helpers\Format;use App\Models\Order;use Carbon\Carbon)
@use(Carbon\WeekDay)
@use(Illuminate\Support\Str)
<div class="{{$class}}">
    @php
        function getWeeksOfTheYear(?int $year = null)
        {
            $year = $year ?? now()->year;

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
            $_year = now()->year;

            $months = collect();
            for($i = 1; $i<=12;$i++){
                $months->push([
                    'month' => Carbon::create($_year, $i, 1)->format('F'),
                    'monthIndex' => $i
                ]);
            }

            return $months;
        }

        function getYears(){
            // get years, where order exists

            $firstYearDate = Order::orderBy('order_due_at', 'asc')->take(1)->first('order_due_at');
            $firstYearDate ? $firstYearDate->value('order_due_at') : null;

            $hasYear = false;

            $firstYear = null;
            if(!is_null($firstYearDate)){
                $firstYear = Format::getYearFromDate($firstYear);
            }

            $latestYearDate = Order::orderBy('order_due_at', 'desc')->take(1)->first('order_due_at');
            $latestYearDate ? $latestYearDate->value('order_due_at') : null;

            $latestYear = null;
            if(!is_null($latestYearDate)){
                $latestYear = Format::getYearFromDate($latestYear);
                $hasYear = true;
            }

            return $hasYear ? range($firstYear, $latestYear, 1) : array(now()->year);
        }
    @endphp
    <div class="relative"
         x-data="{weekOpen: false, selectedWeek: null, monthOpen: false, selectedMonth: null, yearOpen: false, selectedYear: null}">
        <flux:button.group>
            <flux:button :variant="$activePeriod === 'year' ? 'primary' : 'filled'"
                         x-on:click="yearOpen = !yearOpen; $nextTick(()=>{ const elY = document.getElementById('current-year'); if(elY){ elY.scrollIntoView({block: 'center'}); } });">
                Y &darr;
            </flux:button>
            <flux:button :variant="$activePeriod === 'month' ? 'primary' : 'filled'" x-on:click="monthOpen = !monthOpen; $nextTick(()=>{
                const elM = document.getElementById('current-month');
                if(elM){ elM.scrollIntoView({block: 'center'});}
            })">M &darr;
            </flux:button>
            <flux:button :variant="$activePeriod === 'week' ? 'primary' : 'filled'" x-on:click="weekOpen = !weekOpen; $nextTick(() => {
                    const elW = document.getElementById('current-week');
                        if(elW){ elW.scrollIntoView({block: 'center'});}
                })">W &darr;
            </flux:button>
            <flux:button :variant="$activePeriod === 'today' ? 'primary' : 'filled'" wire:click="setToday">T
            </flux:button>
            <flux:button :variant="$activePeriod === 'yesterday' ? 'primary' : 'filled'" wire:click="setYesterday">Y
            </flux:button>
        </flux:button.group>
        {{--week selector--}}
        <div
            class="absolute z-[220] left-20 border-2 border-neutral-700 rounded-xl dark:bg-zinc-800/80  backdrop-blur-lg p-4 flex flex-col gap-4 w-[90px] h-[300px] overflow-scroll origin-top"
            x-show="weekOpen" x-cloak x-on:click.outside="weekOpen = false" x-transition
        >
            @foreach(getWeeksOfTheYear($this->year) as $index => $week)
                @php
                    $today = now()->toDateString();
                    $isCurrentWeek = $today >= $week['start_date'] && $today <= $week['end_date'];
                @endphp
                <div class="cursor-pointer text-center py-1 px-4 rounded-sm hover:bg-neutral-600/50"
                     x-on:click="selectedWeek = {{$index}}; weekOpen = false"
                     :class="selectedWeek === {{$index}} || (selectedWeek === null) && {{$isCurrentWeek ? 'true' : 'false'}} ? 'border-2 border-accent bg-zinc-900' : 'border-2 border-transparent' "
                     wire:click="setWeek('{{$week['start_date']}}', '{{$week['end_date']}}')"
                     data-week-index="{{$index}}"
                     :id="selectedWeek === {{$index}} || (selectedWeek === null) && {{$isCurrentWeek ? 'true' : 'false'}} ? 'current-week' : ''">
                    {{$week['week']}}
                </div>
            @endforeach
        </div>
        {{--month selector--}}
        <div
            class="absolute z-[220] left-4 border-2 border-neutral-700 rounded-xl dark:bg-zinc-800/80  backdrop-blur-lg p-4 flex flex-col gap-4 w-[90px] h-[300px] overflow-scroll origin-top"
            x-show="monthOpen" x-cloak x-on:click.outside="monthOpen = false"
            x-transition
        >
            @foreach(getMonthsOfTheYear() as $index => $month)
                @php
                    $today = now()->toDateString();
                    $isCurrentMonth = now()->month === $month['monthIndex'];
                @endphp
                <div class="cursor-pointer text-center py-1 rounded-sm hover:bg-neutral-600/50"
                     x-on:click="selectedMonth = {{$index}}; monthOpen = false"
                     :class="selectedMonth === {{$index}} || (selectedMonth === null) && {{$isCurrentMonth ? 'true' : 'false'}} ? 'border-2 border-accent bg-zinc-900' : 'border-2 border-transparent' "
                     wire:click="setMonth('{{$month['monthIndex']}}')"
                     data-week-index="{{$index}}"
                     :id="selectedMonth === {{$index}} || (selectedMonth === null) && {{$isCurrentMonth ? 'true' : 'false'}} ? 'current-month' : ''">
                    {{Str::limit($month['month'], 3, '')}}
                </div>
            @endforeach
        </div>
        {{--year selector--}}
        <div
            class="absolute z-[220] left-4 border-2 border-neutral-700 rounded-xl dark:bg-zinc-800/80  backdrop-blur-lg p-4 flex flex-col gap-4 w-[90px] max-h-[300px] overflow-auto origin-top"
            x-show="yearOpen" x-cloak x-on:click.outside="yearOpen = false"
            x-transition
        >
            @foreach(getYears() as $year)
                @php
                    $isCurrentYear = now()->year == $year;
                @endphp
                <div class="cursor-pointer text-center py-1 rounded-sm hover:bg-neutral-600/50"
                     x-on:click="selectedYear = {{$year}}; yearOpen = false"
                     :class="selectedYear === {{$year}} || (selectedYear === null) && {{$isCurrentYear ? 'true' : 'false'}} ? 'border-2 border-accent bg-zinc-900' : 'border-2 border-transparent' "
                     wire:click="setYear('{{$year}}')"
                     :id="selectedYear === {{$index}} || (selectedYear === null) && {{$isCurrentMonth ? 'true' : 'false'}} ? 'current-year' : ''">
                    {{$year}}
                </div>
            @endforeach
        </div>
    </div>
</div>
