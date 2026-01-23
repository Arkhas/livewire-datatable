<?php

namespace Arkhas\LivewireDatatable\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class Filter
{
    protected string $name;
    protected ?string $label = null;
    protected bool $multiple = false;
    protected array $options = [];
    protected ?Closure $queryCallback = null;
    public string $type = 'dropdown';

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Create a new filter instance.
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Get the filter name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the filter label.
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the filter label.
     */
    public function getLabel(): string
    {
        return $this->label ?? ucfirst(str_replace('_', ' ', $this->name));
    }

    /**
     * Set whether multiple values can be selected.
     */
    public function multiple(bool $multiple = true): static
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Check if multiple values can be selected.
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * Set the filter options.
     */
    public function options(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get the filter options.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Set the global query callback.
     */
    public function query(Closure $callback): static
    {
        $this->queryCallback = $callback;

        return $this;
    }

    /**
     * Apply the filter to a query.
     */
    public function applyToQuery(Builder $query, array $values): void
    {
        if (empty($values)) {
            return;
        }

        // Apply individual option queries
        $query->where(function ($q) use ($values) {
            foreach ($values as $value) {
                $option = $this->getOption($value);
                if ($option) {
                    $option->applyToQuery($q, $value);
                }
            }
        });

        // Apply global query callback
        if ($this->queryCallback) {
            call_user_func($this->queryCallback, $query, $values);
        }
    }

    /**
     * Get an option by name.
     */
    public function getOption(string $name): ?FilterOption
    {
        foreach ($this->options as $option) {
            if ($option->getName() === $name) {
                return $option;
            }
        }

        return null;
    }

    /**
     * Get the count of selected values.
     */
    public function getSelectedCount(array $values): int
    {
        return count($values);
    }

    /**
     * Convert the filter to an array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->getLabel(),
            'type' => $this->type,
            'multiple' => $this->multiple,
            'options' => collect($this->options)->map->toArray()->all(),
        ];
    }
}
