<?php

namespace App\Traits;

trait HasQuickDueFilter
{
    public ?string $due_from = null;
    public ?string $due_to = null;
    protected bool $dispatchAble = true;


    public function setToday(): void
    {
        $this->due_from = now()->addDay()->toDateString();
        $this->due_to = now()->addDay()->toDateString();

        if ($this->dispatchAble && method_exists($this,'dispatchEvent')) {
            $this->dispatchEvent();
        }
    }

    public function setYesterday()
    {
        $this->due_from = now()->toDateString();
        $this->due_to = now()->toDateString();

        if ($this->dispatchAble && method_exists($this,'dispatchEvent')) {
            $this->dispatchEvent();
        }
    }

    public function setWeek(): void
    {
        $this->due_from = now()->startOfWeek()->subDay()->format('Y-m-d');
        $this->due_to = now()->endOfWeek()->subDay()->format('Y-m-d');
        if ($this->dispatchAble && method_exists($this,'dispatchEvent')) {
            $this->dispatchEvent();
        }
    }

    public function setMonth(): void
    {
        $this->due_from = now()->startOfMonth()->toDateString();
        $this->due_to = now()->endOfMonth()->toDateString();
        if ($this->dispatchAble && method_exists($this,'dispatchEvent')) {
            $this->dispatchEvent();
        }
    }

    public function setYear()
    {
        $this->due_from = now()->startOfYear()->toDateString();
        $this->due_to = now()->endOfYear()->toDateString();
        if ($this->dispatchAble && method_exists($this,'dispatchEvent')) {
            $this->dispatchEvent();
        }
    }

}
