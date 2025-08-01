<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormInput extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public string $type = "text", public string $id, public string|null $wireModelLive = null, public string|null $wireModel = null, public string|null $placeholder = null, public string|null $name = null, public bool $noFullWidth = false)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.form-input');
    }
}
