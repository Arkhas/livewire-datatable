<?php

namespace Arkhas\LivewireDatatable\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class DateFilter extends DatePickerFilter
{
    public string $type = 'date';

    /**
     * Create a new date filter instance.
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
        return 'single';
    }

    /**
     * Apply the date filter to a query.
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
        $date = is_array($values) ? ($values[0] ?? null) : $values;
        
        if (!$date) {
            return;
        }

        // Handle Carbon instance
        if ($date instanceof Carbon) {
            $parsedDate = $date;
        } else {
            // Handle string date
            $parsedDate = Carbon::parse($date);
        }

        // If a custom query callback is set, use it instead of the default logic
        if ($this->queryCallback) {
            call_user_func($this->queryCallback, $query, $parsedDate);
        } else {
            $query->whereDate($column, $parsedDate->format('Y-m-d'));
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
        ], $this->getCommonAttributes());
    }
}
