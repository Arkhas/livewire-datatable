<?php

namespace Arkhas\LivewireDatatable\Table\Concerns;

trait HasExport
{
    protected bool $exportable = true;
    protected array $exportFormats = ['csv', 'xlsx'];

    /**
     * Enable or disable export.
     */
    public function exportable(bool $exportable = true): static
    {
        $this->exportable = $exportable;

        return $this;
    }

    /**
     * Check if export is enabled.
     */
    public function isExportable(): bool
    {
        return $this->exportable;
    }

    /**
     * Set available export formats.
     */
    public function exportFormats(array $formats): static
    {
        $this->exportFormats = $formats;

        return $this;
    }

    /**
     * Get available export formats.
     */
    public function getExportFormats(): array
    {
        return $this->exportFormats;
    }
}
