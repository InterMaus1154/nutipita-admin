<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class OrderSummaryDownload extends Component
{

    public ?string $url = null;

    #[On('order-summary-link')]
    public function applyUrl(array $data): void
    {
        $this->url = $data['url'];
    }

    public function render(): View
    {
        return view('livewire.order-summary-download');
    }
}
