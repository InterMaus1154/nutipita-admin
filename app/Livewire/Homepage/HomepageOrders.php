<?php

namespace App\Livewire\Homepage;

use App\Models\Order;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class HomepageOrders extends Component
{
    public bool $isDaytime = true;
    public bool $isNighttime = true;

    public array $dayTimeFilters = [];
    public array $nightTimeFilters = [];

    public bool $hasDayTimeOrders;
    public bool $hasNightTimeOrders;

    public function mount(): void
    {
        $this->dayTimeFilters = [
            'due_from'=>now()->toDateString(),
            'due_to' => now()->toDateString(),
            'daytime_only' => true
        ];

        $this->nightTimeFilters = [
            'due_from' => now()->addDay()->toDateString(),
            'due_to' => now()->addDay()->toDateString(),
            'nighttime_only' => true
        ];

        $this->hasDayTimeOrders = Order::query()
            ->whereDate('order_due_at', '=', $this->dayTimeFilters['due_from'])
            ->where('is_daytime', true)
            ->exists();

        $this->hasNightTimeOrders = Order::query()
            ->whereDate('order_due_at', '=', $this->nightTimeFilters['due_from'])
            ->where('is_daytime', false)
            ->exists();

    }

    #[On('toggle-homepage')]
    public function applyFilter(array $values): void
    {
        $this->isDaytime = $values['is_daytime'];
        $this->isNighttime = $values['is_nighttime'];
    }

    public function render(): View
    {
        return view('livewire.homepage-orders');
    }
}
