<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class DataTable extends Component
{
    public $id;
    public $headers;
    public $data;
    public $actions;
    public $searchable;
    public $sortable;
    public $pageable;
    public $exportable;
    public $responsive;
    public $emptyMessage;
    public $tableClass;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $id = 'datatable',
        $headers = [],
        $data = [],
        $actions = true,
        $searchable = true,
        $sortable = true,
        $pageable = true,
        $exportable = true,
        $responsive = true,
        $emptyMessage = 'No hay datos para mostrar',
        $tableClass = 'table table-striped table-hover'
    ) {
        $this->id = $id;
        $this->headers = is_array($headers) ? $headers : [];
        $this->data = is_array($data) ? $data : [];
        $this->actions = $actions;
        $this->searchable = $searchable;
        $this->sortable = $sortable;
        $this->pageable = $pageable;
        $this->exportable = $exportable;
        $this->responsive = $responsive;
        $this->emptyMessage = $emptyMessage;
        $this->tableClass = $tableClass;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('components.data-table');
    }
}
