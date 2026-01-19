<?php

namespace Arkhas\LivewireDatatable\Table\Concerns;

trait HasPagination
{
    protected int $perPage = 10;
    protected array $perPageOptions = [10, 25, 50, 100];

    /**
     * Set the default per page value.
     */
    public function perPage(int $perPage): static
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * Get the default per page value.
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Set the per page options.
     */
    public function perPageOptions(array $options): static
    {
        $this->perPageOptions = $options;

        return $this;
    }

    /**
     * Get the per page options.
     */
    public function getPerPageOptions(): array
    {
        return $this->perPageOptions;
    }
}
