<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Breadcrumb extends Component
{
    public $breadcrumbs;
    public $page;

    /**
     * Create a new component instance.
     */
    public function __construct($breadcrumbs = [], $page = '')
    {
        $this->breadcrumbs = $breadcrumbs;
        $this->page = $page;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('components.breadcrumb');
    }
}
