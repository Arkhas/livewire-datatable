<?php

namespace Arkhas\LivewireDatatable\Table\Concerns;

trait HasSearch
{
    protected array $searchColumns = [];
    protected ?string $searchPlaceholder = null;
    protected bool $searchFromColumns = true;
    protected bool $searchEnabled = true;

    /**
     * Set the searchable columns.
     */
    public function searchable(array $columns = []): static
    {
        $this->searchEnabled = true;
        
        if (!empty($columns)) {
            $this->searchColumns = $columns;
            $this->searchFromColumns = false;
        }

        return $this;
    }

    /**
     * Disable search.
     */
    public function notSearchable(): static
    {
        $this->searchEnabled = false;

        return $this;
    }

    /**
     * Get the searchable columns.
     */
    public function getSearchColumns(): array
    {
        return $this->searchColumns;
    }

    /**
     * Check if search should use columns automatically.
     */
    public function shouldSearchFromColumns(): bool
    {
        return $this->searchFromColumns && empty($this->searchColumns);
    }

    /**
     * Check if search is enabled.
     */
    public function isSearchable(): bool
    {
        return $this->searchEnabled;
    }

    /**
     * Set the search placeholder.
     */
    public function searchPlaceholder(string $placeholder): static
    {
        $this->searchPlaceholder = $placeholder;

        return $this;
    }

    /**
     * Get the search placeholder.
     */
    public function getSearchPlaceholder(): string
    {
        return $this->searchPlaceholder ?? 'Search...';
    }
}
