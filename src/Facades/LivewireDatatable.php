<?php

namespace Arkhas\LivewireDatatable\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Arkhas\LivewireDatatable\LivewireDatatableManager
 */
class LivewireDatatable extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'livewire-datatable';
    }
}
