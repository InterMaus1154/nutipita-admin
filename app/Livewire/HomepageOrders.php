<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class HomepageOrders extends Component
{
    public bool $is_daytime = true;
    public bool $is_nighttime = true;

    #[On('toggle-homepage')]
    public function applyFilter(array $values): void
    {
        $this->is_daytime = $values['is_daytime'];
        $this->is_nighttime = $values['is_nighttime'];
    }

    public function render(): View
    {
        return view('livewire.homepage-orders');
    }
}
