<?php

namespace App\Livewire\Modal;

use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class ModalContainer extends Component
{

    public array $modalStack = [];

    #[On('modal-open')]
    public function add(string $component, array $componentData = []): void
    {
        $this->modalStack[] = [
            'component' => $component,
            'data' => $componentData,
            'key' => uniqid()
        ];
    }

    #[On('modal-close')]
    public function remove(): void
    {
        array_pop($this->modalStack);
    }

    #[On('modal-clear')]
    public function closeAll(): void
    {
        $this->modalStack = [];
    }

    public function render()
    {
        return view('livewire.modal.modal-container');
    }
}
