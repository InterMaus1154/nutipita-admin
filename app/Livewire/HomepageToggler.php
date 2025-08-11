<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class HomepageToggler extends Component
{

    public bool $is_daytime = true;
    public bool $is_nighttime = true;

    public function dispatchEvent(): void
    {
        $this->dispatch('toggle-homepage', [
            'is_daytime' => $this->is_daytime,
            'is_nighttime' => $this->is_nighttime
        ])
            ->to(HomepageOrders::class);
    }

    public function updated(): void
    {
        $this->dispatchEvent();
    }

    public function setDaytime(): void
    {
        $this->is_daytime = true;
        $this->is_nighttime = false;
        $this->dispatchEvent();
    }

    public function setNighttime(): void
    {
        $this->is_daytime = false;
        $this->is_nighttime = true;
        $this->dispatchEvent();
    }

    public function setBoth(): void
    {
        $this->is_daytime = true;
        $this->is_nighttime = true;
        $this->dispatchEvent();
    }

    public function render(): View
    {
        return view('livewire.homepage-toggler');
    }
}
