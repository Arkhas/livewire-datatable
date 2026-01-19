<?php

namespace Arkhas\LivewireDatatable\Table\Concerns;

trait HasSearch
{
    protected array $searchColumns = [];
    protected ?string $searchPlaceholder = null;

    /**
     * Set the searchable columns.
     */
    public function searchable(array $columns): static
    {
        $this->searchColumns = $columns;

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
     * Check if search is enabled.
     */
    public function isSearchable(): bool
    {
        return !empty($this->searchColumns);
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
