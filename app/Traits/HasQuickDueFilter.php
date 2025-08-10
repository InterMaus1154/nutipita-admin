<?php

namespace App\Traits;

use Carbon\WeekDay;
use Livewire\Attributes\Reactive;

trait HasQuickDueFilter
{
    public ?string $due_from = null;
    public ?string $due_to = null;
    public ?string $activePeriod = null;
    public bool $dispatchAble = true;

    public ?string $afterChangeMethod = null;

    public function setAfterChangeMethod(string $method): void
    {
        $this->afterChangeMethod = $method;
    }

    public function afterChangeAction(): void
    {
        if ($this->dispatchAble && method_exists($this, 'dispatchEvent')) {
            $this->dispatchEvent();
        } else if ($this->afterChangeMethod && method_exists($this, $this->afterChangeMethod)) {
            $this->{$this->afterChangeMethod}();
        }
    }

    public function setToday(): void
    {
        $this->due_from = now()->addDay()->toDateString();
        $this->due_to = now()->addDay()->toDateString();
        $this->activePeriod = "today";

        $this->afterChangeAction();
    }

    public function setYesterday(): void
    {
        $this->due_from = now()->toDateString();
        $this->due_to = now()->toDateString();
        $this->activePeriod = "yesterday";

        $this->afterChangeAction();
    }

    public function setWeek(): void
    {
        $this->due_from = now()->startOfWeek(WeekDay::Sunday)->format('Y-m-d');
        $this->due_to = now()->endOfWeek(WeekDay::Saturday)->format('Y-m-d');
        $this->activePeriod = "week";

        $this->afterChangeAction();
    }

    public function setMonth(): void
    {
        $this->due_from = now()->startOfMonth()->toDateString();
        $this->due_to = now()->endOfMonth()->toDateString();
        $this->activePeriod = "month";

        $this->afterChangeAction();
    }

    public function setYear(): void
    {
        $this->due_from = now()->startOfYear()->toDateString();
        $this->due_to = now()->endOfYear()->toDateString();
        $this->activePeriod = "year";

        $this->afterChangeAction();
    }

}
