<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormLabel extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public string|null $id = null, public string|null $class = null, public string|null $text = null)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.form-label');
    }
}
