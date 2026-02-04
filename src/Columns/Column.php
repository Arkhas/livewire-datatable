<?php

namespace Arkhas\LivewireDatatable\Columns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class Column
{
    protected string $name;
    protected ?string $label = null;
    protected ?string $width = null;
    protected bool $sortable = true;
    protected bool $toggable = true;
    protected bool $hidden = false;
    protected ?Closure $htmlCallback = null;
    protected ?Closure $bladeCallback = null;
    protected ?Closure $iconCallback = null;
    protected ?Closure $filterCallback = null;
    protected ?Closure $exportCallback = null;
    protected ?string $sortColumn = null;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->sortColumn = $name;
    }

    /**
     * Create a new column instance.
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Get the column name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the column label.
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the column label.
     */
    public function getLabel(): string
    {
        return $this->label ?? ucfirst(str_replace(['_', '.'], ' ', $this->name));
    }

    /**
     * Set the column width.
     */
    public function width(string $width): static
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get the column width.
     */
    public function getWidth(): ?string
    {
        return $this->width;
    }

    /**
     * Set whether the column is sortable.
     */
    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * Check if the column is sortable.
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Set the column to sort by.
     */
    public function sortBy(string $column): static
    {
        $this->sortColumn = $column;

        return $this;
    }

    /**
     * Get the sort column.
     */
    public function getSortColumn(): string
    {
        return $this->sortColumn;
    }

    /**
     * Set whether the column is toggable.
     */
    public function toggable(bool $toggable = true): static
    {
        $this->toggable = $toggable;

        return $this;
    }

    /**
     * Check if the column is toggable.
     */
    public function isToggable(): bool
    {
        return $this->toggable;
    }

    /**
     * Set whether the column is hidden.
     */
    public function hidden(bool $hidden = true): static
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Check if the column is hidden.
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Set the HTML callback for rendering cell content.
     */
    public function html(Closure $callback): static
    {
        $this->htmlCallback = $callback;

        return $this;
    }

    /**
     * Set the Blade callback for rendering cell content.
     */
    public function blade(Closure $callback): static
    {
        $this->bladeCallback = $callback;

        return $this;
    }

    /**
     * Get the HTML content for a model.
     */
    public function getHtml(Model $model): string
    {
        if ($this->bladeCallback) {
            $bladeTemplate = (string) call_user_func($this->bladeCallback, $model);
            
            // Generate variable name from model class name (e.g., Task -> task, TestModel -> testModel)
            $variableName = Str::camel(class_basename($model));
            
            return Blade::render($bladeTemplate, [
                'model' => $model,
                $variableName => $model,
            ]);
        }

        if ($this->htmlCallback) {
            return (string) call_user_func($this->htmlCallback, $model);
        }

        // Handle dot notation for relationships
        $value = $this->getValue($model);

        return e($value ?? '');
    }

    /**
     * Get the raw value from a model (supports dot notation).
     */
    public function getValue(Model $model): mixed
    {
        $keys = explode('.', $this->name);
        $value = $model;

        foreach ($keys as $key) {
            if ($value === null) {
                return null;
            }

            $value = $value->{$key} ?? null;
        }

        return $value;
    }

    /**
     * Set the icon callback.
     */
    public function icon(Closure $callback): static
    {
        $this->iconCallback = $callback;

        return $this;
    }

    /**
     * Get the icon for a model.
     */
    public function getIcon(Model $model): ?string
    {
        if ($this->iconCallback) {
            return call_user_func($this->iconCallback, $model);
        }

        return null;
    }

    /**
     * Check if the column has an icon.
     */
    public function hasIcon(): bool
    {
        return $this->iconCallback !== null;
    }

    /**
     * Set the filter callback for this column.
     */
    public function filter(Closure $callback): static
    {
        $this->filterCallback = $callback;

        return $this;
    }

    /**
     * Check if the column has a filter.
     */
    public function hasFilter(): bool
    {
        return $this->filterCallback !== null;
    }

    /**
     * Apply the filter to a query.
     */
    public function applyFilter(Builder $query, mixed $value): void
    {
        if ($this->filterCallback) {
            call_user_func($this->filterCallback, $query, $value);
        }
    }

    /**
     * Set the export callback.
     */
    public function exportAs(Closure $callback): static
    {
        $this->exportCallback = $callback;

        return $this;
    }

    /**
     * Get the export value for a model.
     */
    public function getExportValue(Model $model): mixed
    {
        if ($this->exportCallback) {
            return call_user_func($this->exportCallback, $model);
        }

        return $this->getValue($model);
    }

    /**
     * Convert the column to an array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->getLabel(),
            'width' => $this->width,
            'sortable' => $this->sortable,
            'toggable' => $this->toggable,
            'hidden' => $this->hidden,
            'hasIcon' => $this->hasIcon(),
            'hasFilter' => $this->hasFilter(),
            'type' => 'column',
        ];
    }
}
