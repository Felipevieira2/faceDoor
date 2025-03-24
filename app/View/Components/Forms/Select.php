<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name = '',
        public string $id = '',
        public string $label = '',
        public array $options = [],
        public string|array $selected = '',
        public bool $required = false,
        public bool $multiple = false,
        public string $class = '',
        public ?string $error = null,
        public ?string $xmodel = null,
        public ?string $xbindrequired = null
    ) {
        $this->id = $id ?: $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.select');
    }
}
