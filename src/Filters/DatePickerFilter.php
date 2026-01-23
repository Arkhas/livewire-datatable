<?php

namespace Arkhas\LivewireDatatable\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

abstract class DatePickerFilter extends Filter
{
    protected ?string $column = null;
    protected ?string $min = null;
    protected ?string $max = null;
    protected bool $withToday = false;
    protected bool $selectableHeader = false;
    protected bool $clearable = false;
    protected bool $disabled = false;
    protected bool $invalid = false;
    protected ?string $locale = null;
    protected ?string $placeholder = null;
    protected ?string $openTo = null;
    protected bool $forceOpenTo = false;
    protected ?int $months = null;
    protected ?string $startDay = null;
    protected bool $weekNumbers = false;
    protected bool $withInputs = false;
    protected bool $withConfirmation = false;
    protected ?string $unavailable = null;
    protected bool $fixedWeeks = false;

    /**
     * Set the database column to filter on.
     */
    public function column(string $column): static
    {
        $this->column = $column;

        return $this;
    }

    /**
     * Get the database column.
     */
    public function getColumn(): ?string
    {
        return $this->column ?? $this->name;
    }

    /**
     * Set the minimum selectable date.
     */
    public function min(string $min): static
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Set the maximum selectable date.
     */
    public function max(string $max): static
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Show a "Today" shortcut button.
     */
    public function withToday(bool $enabled = true): static
    {
        $this->withToday = $enabled;

        return $this;
    }

    /**
     * Make the month and year in the header selectable.
     */
    public function selectableHeader(bool $enabled = true): static
    {
        $this->selectableHeader = $enabled;

        return $this;
    }

    /**
     * Show a clear button when a date is selected.
     */
    public function clearable(bool $enabled = true): static
    {
        $this->clearable = $enabled;

        return $this;
    }

    /**
     * Disable the date picker.
     */
    public function disabled(bool $enabled = true): static
    {
        $this->disabled = $enabled;

        return $this;
    }

    /**
     * Apply error styling.
     */
    public function invalid(bool $enabled = true): static
    {
        $this->invalid = $enabled;

        return $this;
    }

    /**
     * Set the locale for the date picker.
     */
    public function locale(string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Set the placeholder text.
     */
    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Set the date that the date picker will open to.
     */
    public function openTo(string $date): static
    {
        $this->openTo = $date;

        return $this;
    }

    /**
     * Force the date picker to open to the open-to date.
     */
    public function forceOpenTo(bool $enabled = true): static
    {
        $this->forceOpenTo = $enabled;

        return $this;
    }

    /**
     * Set the number of months to display.
     */
    public function months(int $months): static
    {
        $this->months = $months;

        return $this;
    }

    /**
     * Set the day of the week to start the calendar on (0-6, Sunday-Saturday).
     */
    public function startDay(int $day): static
    {
        $this->startDay = (string) $day;

        return $this;
    }

    /**
     * Display week numbers in the calendar.
     */
    public function weekNumbers(bool $enabled = true): static
    {
        $this->weekNumbers = $enabled;

        return $this;
    }

    /**
     * Display date inputs at the top of the calendar.
     */
    public function withInputs(bool $enabled = true): static
    {
        $this->withInputs = $enabled;

        return $this;
    }

    /**
     * Require confirmation before applying the selected date(s).
     */
    public function withConfirmation(bool $enabled = true): static
    {
        $this->withConfirmation = $enabled;

        return $this;
    }

    /**
     * Set unavailable dates (comma-separated list).
     */
    public function unavailable(string $dates): static
    {
        $this->unavailable = $dates;

        return $this;
    }

    /**
     * Display a consistent number of weeks in every month.
     */
    public function fixedWeeks(bool $enabled = true): static
    {
        $this->fixedWeeks = $enabled;

        return $this;
    }

    /**
     * Get the filter mode (must be implemented by child classes).
     */
    abstract public function getMode(): string;

    /**
     * Get common attributes for the date picker.
     */
    protected function getCommonAttributes(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
            'withToday' => $this->withToday,
            'selectableHeader' => $this->selectableHeader,
            'clearable' => $this->clearable,
            'disabled' => $this->disabled,
            'invalid' => $this->invalid,
            'locale' => $this->locale,
            'placeholder' => $this->placeholder,
            'openTo' => $this->openTo,
            'forceOpenTo' => $this->forceOpenTo,
            'months' => $this->months,
            'startDay' => $this->startDay,
            'weekNumbers' => $this->weekNumbers,
            'withInputs' => $this->withInputs,
            'withConfirmation' => $this->withConfirmation,
            'unavailable' => $this->unavailable,
            'fixedWeeks' => $this->fixedWeeks,
        ];
    }

    // Getters for Blade access
    public function getWithToday(): bool { return $this->withToday; }
    public function getSelectableHeader(): bool { return $this->selectableHeader; }
    public function getClearable(): bool { return $this->clearable; }
    public function getDisabled(): bool { return $this->disabled; }
    public function getInvalid(): bool { return $this->invalid; }
    public function getForceOpenTo(): bool { return $this->forceOpenTo; }
    public function getWeekNumbers(): bool { return $this->weekNumbers; }
    public function getWithInputs(): bool { return $this->withInputs; }
    public function getWithConfirmation(): bool { return $this->withConfirmation; }
    public function getFixedWeeks(): bool { return $this->fixedWeeks; }
    public function getMin(): ?string { return $this->min; }
    public function getMax(): ?string { return $this->max; }
    public function getLocale(): ?string { return $this->locale; }
    public function getPlaceholder(): ?string { return $this->placeholder; }
    public function getOpenTo(): ?string { return $this->openTo; }
    public function getMonths(): ?int { return $this->months; }
    public function getStartDay(): ?string { return $this->startDay; }
    public function getUnavailable(): ?string { return $this->unavailable; }
}
