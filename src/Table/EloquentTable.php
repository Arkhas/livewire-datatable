<?php

namespace Arkhas\LivewireDatatable\Table;

use Arkhas\LivewireDatatable\Table\Concerns\HasColumns;
use Arkhas\LivewireDatatable\Table\Concerns\HasFilters;
use Arkhas\LivewireDatatable\Table\Concerns\HasActions;
use Arkhas\LivewireDatatable\Table\Concerns\HasPagination;
use Arkhas\LivewireDatatable\Table\Concerns\HasSearch;
use Arkhas\LivewireDatatable\Table\Concerns\HasExport;
use Illuminate\Database\Schema\Builder;

class EloquentTable
{
    use HasColumns;
    use HasFilters;
    use HasActions;
    use HasPagination;
    use HasSearch;
    use HasExport;

    protected Builder $query;
    protected string $exportName = 'export';

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    /**
     * Get the base query builder.
     */
    public function getQuery(): Builder
    {
        return clone $this->query;
    }

    /**
     * Get the model class name.
     */
    public function getModel(): string
    {
        return get_class($this->query->getModel());
    }

    /**
     * Set the export filename.
     */
    public function exportName(string $name): static
    {
        $this->exportName = $name;

        return $this;
    }

    /**
     * Get the export filename.
     */
    public function getExportName(): string
    {
        return $this->exportName;
    }

    /**
     * Build the final query with all filters, search, and sorting applied.
     */
    public function buildQuery(array $filters = [], ?string $search = null, ?string $sortColumn = null, string $sortDirection = 'asc'): Builder
    {
        $query = $this->getQuery();

        // Apply search
        if ($search && $this->isSearchable()) {
            $searchColumns = $this->getResolvedSearchColumns();
            
            $query->where(function ($q) use ($search, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $this->applySearchCondition($q, $column, $search);
                }
            });
        }

        // Apply filters
        foreach ($filters as $filterName => $values) {
            $filter = $this->getFilter($filterName);
            if ($filter && !empty($values)) {
                $filter->applyToQuery($query, $values);
            }
        }

        // Apply column-level filters
        foreach ($this->columns as $column) {
            if ($column->hasFilter() && isset($filters[$column->getName()])) {
                $column->applyFilter($query, $filters[$column->getName()]);
            }
        }

        // Apply sorting
        if ($sortColumn) {
            $column = $this->getColumn($sortColumn);
            if ($column && $column->isSortable()) {
                $query->orderBy($column->getSortColumn(), $sortDirection);
            }
        }

        return $query;
    }

    /**
     * Get paginated results.
     */
    public function paginate(array $filters = [], ?string $search = null, ?string $sortColumn = null, string $sortDirection = 'asc', int $perPage = 10)
    {
        return $this->buildQuery($filters, $search, $sortColumn, $sortDirection)
            ->paginate($perPage);
    }

    /**
     * Convert the table configuration to an array for the frontend.
     */
    public function toArray(): array
    {
        return [
            'columns' => collect($this->columns)->map->toArray()->all(),
            'filters' => collect($this->filters)->map->toArray()->all(),
            'actions' => collect($this->actions)->map->toArray()->all(),
            'exportName' => $this->exportName,
            'searchable' => $this->isSearchable(),
        ];
    }

    /**
     * Get the resolved search columns (either explicit or from columns).
     */
    protected function getResolvedSearchColumns(): array
    {
        // If explicit search columns are defined, use them
        if (!empty($this->searchColumns)) {
            return $this->searchColumns;
        }

        // If searchFromColumns is enabled, get column names from defined columns
        if ($this->shouldSearchFromColumns()) {
            return collect($this->columns)
                ->filter(function ($column) {
                    // Exclude special columns (ActionColumn, CheckboxColumn)
                    return !str_starts_with($column->getName(), '__');
                })
                ->map(fn($column) => $column->getName())
                ->values()
                ->all();
        }

        return [];
    }

    /**
     * Apply a search condition for a column (handles relationships).
     */
    protected function applySearchCondition(Builder $query, string $column, string $search): void
    {
        // Check if column contains a dot (relation)
        if (str_contains($column, '.')) {
            $parts = explode('.', $column);
            $columnName = array_pop($parts);
            $relation = implode('.', $parts);
            
            $query->orWhereRelation($relation, $columnName, 'like', "%{$search}%");
        } else {
            $query->orWhere($column, 'like', "%{$search}%");
        }
    }
}
