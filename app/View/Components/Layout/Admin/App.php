<?php

namespace App\View\Components\Layout\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class App extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title = 'FaceDoor',
        public bool $showHeader = true,
        public bool $showFooter = true
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
       
        return view('components.layout.admin.app');
    }
}
