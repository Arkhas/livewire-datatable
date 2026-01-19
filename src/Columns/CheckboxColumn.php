<?php

namespace Arkhas\LivewireDatatable\Columns;

class CheckboxColumn extends Column
{
    public function __construct()
    {
        parent::__construct('__checkbox');
        
        $this->sortable(false);
        $this->toggable(false);
        $this->label('');
    }

    /**
     * Create a new checkbox column instance.
     */
    public static function make(): static
    {
        return new static();
    }

    /**
     * Convert the column to an array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'type' => 'checkbox',
        ]);
    }
}
