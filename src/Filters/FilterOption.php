<?php

namespace Arkhas\LivewireDatatable\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;

class FilterOption
{
    protected string $name;
    protected ?string $label = null;
    protected ?string $icon = null;
    protected ?Closure $countCallback = null;
    protected ?Closure $queryCallback = null;
    protected ?int $count = null;
    protected bool $isDefault = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Create a new filter option instance.
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Get the option name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the option label.
     */
    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the option label.
     */
    public function getLabel(): string
    {
        return $this->label ?? ucfirst(str_replace('_', ' ', $this->name));
    }

    /**
     * Set the option icon.
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the option icon.
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * Set the count callback or direct count value.
     */
    public function count(Closure|int $count): static
    {
        if (is_int($count)) {
            $this->count = $count;
        } else {
            $this->countCallback = $count;
        }

        return $this;
    }

    /**
     * Get the count for this option.
     */
    public function getCount(): ?int
    {
        if ($this->count !== null) {
            return $this->count;
        }

        if ($this->countCallback) {
            $this->count = call_user_func($this->countCallback);
            return $this->count;
        }

        $this->count = null;
        return null;
    }

    /**
     * Set the query callback.
     */
    public function query(Closure $callback): static
    {
        $this->queryCallback = $callback;

        return $this;
    }

    /**
     * Mark this option as default.
     */
    public function default(bool $isDefault = true): static
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Check if this option is default.
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * Apply the option query to a builder.
     */
    public function applyToQuery(Builder $query, string $keyword): void
    {
        if ($this->queryCallback) {
            $query->orWhere(function ($q) use ($keyword) {
                call_user_func($this->queryCallback, $q, $keyword);
            });
        }
    }

    /**
     * Convert the option to an array.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->getLabel(),
            'icon' => $this->icon,
            'count' => $this->getCount(),
            'isDefault' => $this->isDefault,
        ];
    }
}
