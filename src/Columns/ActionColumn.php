<?php

namespace Arkhas\LivewireDatatable\Columns;

use Arkhas\LivewireDatatable\Actions\ColumnAction;
use Arkhas\LivewireDatatable\Actions\ColumnActionGroup;

class ActionColumn extends Column
{
    protected ColumnAction|ColumnActionGroup|null $action = null;

    public function __construct(?string $name = null)
    {
        parent::__construct($name ?? '__actions');
        
        $this->sortable(false);
        $this->toggable(false);
        $this->label('');
    }

    /**
     * Create a new action column instance.
     */
    public static function make(?string $name = null): static
    {
        return new static($name);
    }

    /**
     * Set the action(s) for this column.
     */
    public function action(ColumnAction|ColumnActionGroup $action): static
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get the action.
     */
    public function getAction(): ColumnAction|ColumnActionGroup|null
    {
        return $this->action;
    }

    /**
     * Convert the column to an array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'type' => 'action',
            'action' => $this->action?->toArray(),
        ]);
    }
}
