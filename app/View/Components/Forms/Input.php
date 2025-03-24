<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $type = 'text',
        public string $name = '',
        public string $id = '',
        public ?string $label = null,
        public string $placeholder = '',
        public ?string $value = null,
        public bool $required = false,
        public string $class = '',
        public ?string $error = null,
        public ?string $autocomplete = null,
        public ?string $xbindrequired = null
    ) {
        $this->id = $id ?: $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.input');
    }
}
