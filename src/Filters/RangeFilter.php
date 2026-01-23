<?php

namespace Arkhas\LivewireDatatable\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class RangeFilter extends DatePickerFilter
{
    public string $type = 'range';
    protected bool $withPresets = false;
    protected ?string $presets = null;
    protected ?int $minRange = null;
    protected ?int $maxRange = null;

    /**
     * Create a new range filter instance.
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Get the filter mode.
     */
    public function getMode(): string
    {
        return 'range';
    }

    /**
     * Enable preset date ranges.
     */
    public function withPresets(bool $enabled = true): static
    {
        $this->withPresets = $enabled;

        return $this;
    }

    /**
     * Set which presets to show (space-separated list).
     */
    public function presets(string $presets): static
    {
        $this->presets = $presets;

        return $this;
    }

    /**
     * Set the minimum number of days in range.
     */
    public function minRange(int $days): static
    {
        $this->minRange = $days;

        return $this;
    }

    /**
     * Set the maximum number of days in range.
     */
    public function maxRange(int $days): static
    {
        $this->maxRange = $days;

        return $this;
    }

    // Getters for Blade access
    public function getWithPresets(): bool { return $this->withPresets; }
    public function getPresets(): ?string { return $this->presets; }
    public function getMinRange(): ?int { return $this->minRange; }
    public function getMaxRange(): ?int { return $this->maxRange; }

    /**
     * Apply the range filter to a query.
     */
    public function applyToQuery(Builder $query, array|string $values): void
    {
        // Normalize values to array if it's a string
        if (is_string($values)) {
            $values = [$values];
        }

        if (empty($values)) {
            return;
        }

        $column = $this->getColumn();
        
        $startDate = null;
        $endDate = null;

        // Handle DateRange object (from Flux Pro)
        if (isset($values[0]) && is_object($values[0]) && method_exists($values[0], 'start') && method_exists($values[0], 'end')) {
            $startDate = Carbon::parse($values[0]->start())->startOfDay();
            $endDate = Carbon::parse($values[0]->end())->endOfDay();
        }
        // Handle string format 'Y-m-d/Y-m-d'
        elseif (isset($values[0]) && is_string($values[0]) && str_contains($values[0], '/')) {
            [$start, $end] = explode('/', $values[0], 2);
            $startDate = Carbon::parse($start)->startOfDay();
            $endDate = Carbon::parse($end)->endOfDay();
        }
        // Handle array with 'start' and 'end' keys
        elseif (isset($values['start']) && isset($values['end'])) {
            $startDate = Carbon::parse($values['start'])->startOfDay();
            $endDate = Carbon::parse($values['end'])->endOfDay();
        }
        // Handle array where first element is an array with 'start' and 'end'
        elseif (isset($values[0]) && is_array($values[0]) && isset($values[0]['start']) && isset($values[0]['end'])) {
            $startDate = Carbon::parse($values[0]['start'])->startOfDay();
            $endDate = Carbon::parse($values[0]['end'])->endOfDay();
        }

        if ($startDate && $endDate) {
            // If a custom query callback is set, use it instead of the default logic
            if ($this->queryCallback) {
                call_user_func($this->queryCallback, $query, [$startDate, $endDate]);
            } else {
                $query->whereBetween($column, [$startDate, $endDate]);
            }
        }
    }

    /**
     * Convert the filter to an array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'type' => $this->type,
            'mode' => $this->getMode(),
            'column' => $this->getColumn(),
            'withPresets' => $this->withPresets,
            'presets' => $this->presets,
            'minRange' => $this->minRange,
            'maxRange' => $this->maxRange,
        ], $this->getCommonAttributes());
    }
}
