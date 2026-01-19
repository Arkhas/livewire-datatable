<?php

namespace Arkhas\LivewireDatatable\Table\Concerns;

use Arkhas\LivewireDatatable\Columns\Column;

trait HasColumns
{
    protected array $columns = [];

    /**
     * Set the columns for this table.
     */
    public function columns(array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Get all columns.
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get visible columns.
     */
    public function getVisibleColumns(): array
    {
        return array_filter($this->columns, fn($column) => !$column->isHidden());
    }

    /**
     * Get a column by name.
     */
    public function getColumn(string $name): ?Column
    {
        foreach ($this->columns as $column) {
            if ($column->getName() === $name) {
                return $column;
            }
        }

        return null;
    }

    /**
     * Get sortable columns.
     */
    public function getSortableColumns(): array
    {
        return array_filter($this->columns, fn($column) => $column->isSortable());
    }

    /**
     * Get toggable columns.
     */
    public function getToggableColumns(): array
    {
        return array_filter($this->columns, fn($column) => $column->isToggable());
    }
}
