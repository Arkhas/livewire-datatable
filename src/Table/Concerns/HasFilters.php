<?php

namespace Arkhas\LivewireDatatable\Table\Concerns;

use Arkhas\LivewireDatatable\Filters\Filter;

trait HasFilters
{
    protected array $filters = [];

    /**
     * Set the filters for this table.
     */
    public function filters(array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Get all filters.
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Get a filter by name.
     */
    public function getFilter(string $name): ?Filter
    {
        foreach ($this->filters as $filter) {
            if ($filter->getName() === $name) {
                return $filter;
            }
        }

        return null;
    }

    /**
     * Get active filters count.
     */
    public function getActiveFiltersCount(array $filterValues): int
    {
        $count = 0;

        foreach ($filterValues as $name => $values) {
            if (!empty($values)) {
                $count++;
            }
        }

        return $count;
    }
}
