<?php

namespace App\Livewire\Modal;

use Livewire\Component;

class OrderCreate extends Component
{

    public function cancel(): void
    {
        $this->reset();
        $this->dispatch('modal-clear');
    }

    public function render()
    {
        return view('livewire.modal.order-create');
    }
}
