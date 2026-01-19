<?php

namespace Arkhas\LivewireDatatable\Traits;

use Livewire\WithPagination;
use Arkhas\LivewireDatatable\Table\EloquentTable;

trait WithDatatable
{
    use WithPagination;

    public ?string $search = null;
    public array $filters = [];
    public ?string $sortColumn = null;
    public string $sortDirection = 'asc';
    public int $perPage = 10;
    public array $selected = [];
    public bool $selectAll = false;
    public array $hiddenColumns = [];

    // Confirmation modal state
    public bool $showConfirmModal = false;
    public ?string $pendingAction = null;
    public ?string $pendingActionType = null;
    public mixed $pendingActionTarget = null;
    public array $confirmData = [];

    protected EloquentTable $eloquentTable;

    /**
     * Initialize the datatable.
     */
    public function bootWithDatatable(): void
    {
        $this->perPage = config('livewire-datatable.per_page', 10);
    }

    /**
     * Mount hook for the trait.
     */
    public function mountWithDatatable(): void
    {
        $this->setup();
    }

    /**
     * Abstract method to setup the table configuration.
     */
    abstract public function setup(): void;

    /**
     * Set the table configuration.
     */
    protected function table(EloquentTable $table): void
    {
        $this->eloquentTable = $table;
    }

    /**
     * Get the table configuration.
     */
    public function getTable(): EloquentTable
    {
        if (!isset($this->eloquentTable)) {
            $this->setup();
        }

        return $this->eloquentTable;
    }

    /**
     * Get the paginated data.
     */
    public function getData()
    {
        return $this->getTable()->paginate(
            $this->filters,
            $this->search,
            $this->sortColumn,
            $this->sortDirection,
            $this->perPage
        );
    }

    /**
     * Sort by a column.
     */
    public function sortBy(string $column): void
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = config('livewire-datatable.default_sort_direction', 'asc');
        }

        $this->resetPage();
    }

    /**
     * Apply a filter.
     */
    public function applyFilter(string $filter, array $values): void
    {
        $this->filters[$filter] = $values;
        $this->resetPage();
    }

    /**
     * Remove a filter.
     */
    public function removeFilter(string $filter): void
    {
        unset($this->filters[$filter]);
        $this->resetPage();
    }

    /**
     * Reset all filters.
     */
    public function resetFilters(): void
    {
        $this->filters = [];
        $this->resetPage();
    }

    /**
     * Toggle a filter value.
     */
    public function toggleFilter(string $filter, string $value): void
    {
        if (!isset($this->filters[$filter])) {
            $this->filters[$filter] = [];
        }

        $filterConfig = $this->getTable()->getFilter($filter);
        $isMultiple = $filterConfig && $filterConfig->isMultiple();

        if (in_array($value, $this->filters[$filter])) {
            $this->filters[$filter] = array_values(array_diff($this->filters[$filter], [$value]));
        } else {
            if ($isMultiple) {
                $this->filters[$filter][] = $value;
            } else {
                $this->filters[$filter] = [$value];
            }
        }

        $this->resetPage();
    }

    /**
     * Toggle row selection.
     */
    public function toggleSelect(mixed $id): void
    {
        if (in_array($id, $this->selected)) {
            $this->selected = array_values(array_diff($this->selected, [$id]));
            $this->selectAll = false;
        } else {
            $this->selected[] = $id;
        }
    }

    /**
     * Toggle select all on current page.
     */
    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selected = [];
            $this->selectAll = false;
        } else {
            $this->selected = $this->getData()->pluck('id')->toArray();
            $this->selectAll = true;
        }
    }

    /**
     * Clear selection.
     */
    public function clearSelection(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    /**
     * Toggle column visibility.
     */
    public function toggleColumn(string $column): void
    {
        if (in_array($column, $this->hiddenColumns)) {
            $this->hiddenColumns = array_values(array_diff($this->hiddenColumns, [$column]));
        } else {
            $this->hiddenColumns[] = $column;
        }
    }

    /**
     * Check if a column is visible.
     */
    public function isColumnVisible(string $column): bool
    {
        return !in_array($column, $this->hiddenColumns);
    }

    /**
     * Execute a bulk action.
     */
    public function executeBulkAction(string $actionName): void
    {
        if (empty($this->selected)) {
            return;
        }

        $action = $this->getTable()->getAction($actionName);

        if (!$action) {
            return;
        }

        // Check if confirmation is required
        if ($action->requiresConfirmation()) {
            $this->pendingAction = $actionName;
            $this->pendingActionType = 'bulk';
            $this->pendingActionTarget = $this->selected;
            $this->confirmData = $action->getConfirmation($this->selected);
            $this->showConfirmModal = true;
            return;
        }

        $this->performBulkAction($actionName);
    }

    /**
     * Actually perform the bulk action.
     */
    protected function performBulkAction(string $actionName): void
    {
        $result = $this->getTable()->executeAction($actionName, $this->selected);

        $this->handleActionResult($result);
        $this->clearSelection();
    }

    /**
     * Execute a row action.
     */
    public function executeRowAction(string $actionName, mixed $rowId): void
    {
        $table = $this->getTable();
        $model = $table->getQuery()->find($rowId);

        if (!$model) {
            return;
        }

        // Find the action in ActionColumn
        $action = null;
        foreach ($table->getColumns() as $column) {
            if ($column instanceof \Arkhas\LivewireDatatable\Columns\ActionColumn) {
                $columnAction = $column->getAction();
                if ($columnAction instanceof \Arkhas\LivewireDatatable\Actions\ColumnActionGroup) {
                    $action = $columnAction->getAction($actionName);
                } elseif ($columnAction instanceof \Arkhas\LivewireDatatable\Actions\ColumnAction && $columnAction->getName() === $actionName) {
                    $action = $columnAction;
                }
            }
        }

        if (!$action) {
            return;
        }

        // Check if confirmation is required
        if ($action->requiresConfirmation()) {
            $this->pendingAction = $actionName;
            $this->pendingActionType = 'row';
            $this->pendingActionTarget = $rowId;
            $this->confirmData = $action->getConfirmation($model);
            $this->showConfirmModal = true;
            return;
        }

        $this->performRowAction($actionName, $rowId);
    }

    /**
     * Actually perform the row action.
     */
    protected function performRowAction(string $actionName, mixed $rowId): void
    {
        $table = $this->getTable();
        $model = $table->getQuery()->find($rowId);

        if (!$model) {
            return;
        }

        // Find and execute the action
        foreach ($table->getColumns() as $column) {
            if ($column instanceof \Arkhas\LivewireDatatable\Columns\ActionColumn) {
                $columnAction = $column->getAction();
                if ($columnAction instanceof \Arkhas\LivewireDatatable\Actions\ColumnActionGroup) {
                    $action = $columnAction->getAction($actionName);
                    if ($action) {
                        $result = $action->execute($model);
                        $this->handleActionResult($result);
                        return;
                    }
                } elseif ($columnAction instanceof \Arkhas\LivewireDatatable\Actions\ColumnAction && $columnAction->getName() === $actionName) {
                    $result = $columnAction->execute($model);
                    $this->handleActionResult($result);
                    return;
                }
            }
        }
    }

    /**
     * Confirm and execute the pending action.
     */
    public function confirmAction(): void
    {
        if (!$this->pendingAction) {
            $this->cancelConfirm();
            return;
        }

        if ($this->pendingActionType === 'bulk') {
            $this->performBulkAction($this->pendingAction);
        } else {
            $this->performRowAction($this->pendingAction, $this->pendingActionTarget);
        }

        $this->cancelConfirm();
    }

    /**
     * Cancel the pending action.
     */
    public function cancelConfirm(): void
    {
        $this->showConfirmModal = false;
        $this->pendingAction = null;
        $this->pendingActionType = null;
        $this->pendingActionTarget = null;
        $this->confirmData = [];
    }

    /**
     * Handle action result.
     */
    protected function handleActionResult(array $result): void
    {
        if (isset($result['success']) && $result['success']) {
            if (isset($result['message'])) {
                $this->dispatch('notify', [
                    'type' => 'success',
                    'title' => $result['title'] ?? 'Success',
                    'message' => $result['message'],
                ]);
            }
        } else {
            if (isset($result['message'])) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'title' => $result['title'] ?? 'Error',
                    'message' => $result['message'],
                ]);
            }
        }
    }

    /**
     * Update the search query.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Update the per page value.
     */
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    /**
     * Get active filters count.
     */
    public function getActiveFiltersCount(): int
    {
        return $this->getTable()->getActiveFiltersCount($this->filters);
    }

    /**
     * Export the data.
     */
    public function export(string $format = 'csv')
    {
        $exporter = new \Arkhas\LivewireDatatable\Export\DatatableExporter(
            $this->getTable(),
            $this->filters,
            $this->search
        );

        return $exporter->export($format);
    }
}
