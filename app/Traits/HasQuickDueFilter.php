<?php

namespace App\Traits;

use Carbon\Carbon;
use Carbon\WeekDay;
use Livewire\Attributes\Reactive;

trait HasQuickDueFilter
{
    public array $months = [
        '1' => 'January',
        '2' => 'February',
        '3' => 'March',
        '4' => 'April',
        '5' => 'May',
        '6' => 'June',
        '7' => 'July',
        '8' => 'August',
        '9' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
    ];

    public ?string $due_from = null;
    public ?string $due_to = null;
    public ?string $activePeriod = null;
    public bool $dispatchAble = true;

    public ?string $afterChangeMethod = null;

    public ?int $year;


    public function setAfterChangeMethod(string $method): void
    {
        $this->afterChangeMethod = $method;
    }

    public function afterChangeAction(): void
    {

        if ($this->afterChangeMethod && method_exists($this, $this->afterChangeMethod)) {
            $this->{$this->afterChangeMethod}();
        } else if ($this->dispatchAble && method_exists($this, 'dispatchEvent')) {
            $this->dispatchEvent();
        }
    }

    public function setToday(): void
    {
        $this->due_from = now()->addDay()->toDateString();
        $this->due_to = now()->addDay()->toDateString();
        $this->activePeriod = "today";
        $this->year = now()->year;

        $this->afterChangeAction();
    }

    public function setYesterday(): void
    {
        $this->due_from = now()->toDateString();
        $this->due_to = now()->toDateString();
        $this->activePeriod = "yesterday";

        $this->afterChangeAction();
    }

    public function setWeek(string $weekStart, string $weekEnd): void
    {
        $this->due_from = $weekStart;
        $this->due_to = $weekEnd;
        $this->activePeriod = "week";

        $this->afterChangeAction();
    }

    public function setCurrentWeek(): void
    {
        $this->due_from = now()->startOfWeek()->format('Y-m-d');
        $this->due_to = now()->endOfWeek()->format('Y-m-d');

        $this->activePeriod = "week";
        $this->afterChangeAction();
    }

    public function setYear(int $year): void
    {
        $this->due_from = sprintf('%04d-%02d-%02d', $year, 1, 1);
        $this->due_to = sprintf('%04d-%02d-%02d', $year, 12, 31);

        $this->year = $year;
        $this->activePeriod = 'year';
        $this->afterChangeAction();
    }

    public function setMonth(int $monthIndex): void
    {
        $year = $this->year ?? now()->year;
        $this->due_from = Carbon::create($year, $monthIndex, 1)->startOfMonth()->toDateString();
        $this->due_to = Carbon::create($year, $monthIndex, 1)->endOfMonth()->toDateString();
        $this->activePeriod = "month";

        $this->afterChangeAction();
    }

    public function setCurrentMonth(): void
    {
        $this->due_from = now()->startOfMonth()->toDateString();
        $this->due_to = now()->endOfMonth()->toDateString();
        $this->activePeriod = "month";

        $this->afterChangeAction();
    }


//    public function setYear(): void
//    {
//        $this->due_from = now()->startOfYear()->toDateString();
//        $this->due_to = now()->endOfYear()->toDateString();
//        $this->activePeriod = "year";
//
//        $this->afterChangeAction();
//    }

}
