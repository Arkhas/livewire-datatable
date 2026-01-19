<?php

namespace Arkhas\LivewireDatatable\Components;

use Livewire\Component;
use Arkhas\LivewireDatatable\Traits\WithDatatable;

class Datatable extends Component
{
    use WithDatatable;

    /**
     * Setup is handled by the parent component that uses WithDatatable trait.
     * This component is just a base implementation.
     */
    public function setup(): void
    {
        // Override in child components
    }

    /**
     * Render the datatable.
     */
    public function render()
    {
        return view('livewire-datatable::datatable', [
            'table' => $this->getTable(),
            'data' => $this->getData(),
        ]);
    }
}
