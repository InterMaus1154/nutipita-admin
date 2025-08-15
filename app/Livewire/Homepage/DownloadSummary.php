<?php

namespace App\Livewire\Homepage;

use App\Livewire\OrderList;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class DownloadSummary extends Component
{
    /**
     *
     * @param string $type - day, night, both
     */
    public function createSummary(string $type)
    {
        $due_from = "";
        $due_to = "";
        $nighttime_only = false;
        $daytime_only = false;
        $both = false;
        switch ($type) {
            case "night":
                $due_from = now()->addDay()->toDateString();
                $due_to = now()->addDay()->toDateString();
                $nighttime_only = true;
                break;
            case "day":
                $due_from = now()->toDateString();
                $due_to = now()->toDateString();
                $daytime_only = true;
                break;
            case "both":
                $due_from = now()->toDateString();
                $due_to = now()->toDateString();
                $both = true;
                break;
        }

        $summaryDto = (new OrderService)->createBasicSummary([
            'due_from' => $due_from,
            'due_to' => $due_to,
            'daytime_only' => $daytime_only,
            'nighttime_only' => $nighttime_only,
            'both' => $both
        ]);

        session()->flash('dto', $summaryDto);
        $this->redirectRoute('today.order.pdf', [
            'type' => $type
        ]);
    }


    public function render(): View
    {
        return view('livewire.homepage.download-summary');
    }
}
