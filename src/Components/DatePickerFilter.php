<?php

namespace Arkhas\LivewireDatatable\Components;

use Arkhas\LivewireDatatable\Filters\DatePickerFilter as DatePickerFilterFilter;
use Arkhas\LivewireDatatable\Filters\RangeFilter;
use Illuminate\View\Component;

class DatePickerFilter extends Component
{
    public DatePickerFilterFilter $filter;
    public array $filters;
    public string $wireModel;
    public ?string $formattedValue;
    public string $dateMode;
    
    // Expose filter properties directly for Blade
    public ?string $min;
    public ?string $max;
    public ?bool $withToday;
    public ?bool $selectableHeader;
    public ?bool $clearable;
    public ?bool $disabled;
    public ?bool $invalid;
    public ?string $locale;
    public ?string $placeholder;
    public ?string $openTo;
    public ?bool $forceOpenTo;
    public ?int $months;
    public ?string $startDay;
    public ?bool $weekNumbers;
    public ?bool $withInputs;
    public ?bool $withConfirmation;
    public ?string $unavailable;
    public ?bool $fixedWeeks;
    
    // Range-specific properties
    public ?bool $withPresets;
    public ?string $presets;
    public ?int $minRange;
    public ?int $maxRange;

    /**
     * Create a new component instance.
     */
    public function __construct(DatePickerFilterFilter $filter, array $filters = [])
    {
        $this->filter = $filter;
        $this->filters = $filters;
        $this->dateMode = $filter->getMode();
        
        // Format value for date picker
        $dateValue = isset($filters[$filter->getName()]) ? $filters[$filter->getName()] : null;
        
        if ($dateValue) {
            if ($this->dateMode === 'range') {
                if (is_array($dateValue) && isset($dateValue['start']) && isset($dateValue['end'])) {
                    $this->formattedValue = $dateValue['start'] . '/' . $dateValue['end'];
                } elseif (is_array($dateValue) && isset($dateValue[0]) && str_contains($dateValue[0], '/')) {
                    $this->formattedValue = $dateValue[0];
                } else {
                    $this->formattedValue = null;
                }
            } else {
                $this->formattedValue = is_array($dateValue) ? ($dateValue[0] ?? null) : $dateValue;
            }
        } else {
            $this->formattedValue = null;
        }
        
        $this->wireModel = "filters.{$filter->getName()}";
        
        // Expose all filter properties
        $this->min = $filter->getMin();
        $this->max = $filter->getMax();
        $this->withToday = $filter->getWithToday();
        $this->selectableHeader = $filter->getSelectableHeader();
        $this->clearable = $filter->getClearable();
        $this->disabled = $filter->getDisabled();
        $this->invalid = $filter->getInvalid();
        $this->locale = $filter->getLocale();
        $this->placeholder = $filter->getPlaceholder() ?? $filter->getLabel();
        $this->openTo = $filter->getOpenTo();
        $this->forceOpenTo = $filter->getForceOpenTo();
        $this->months = $filter->getMonths();
        $this->startDay = $filter->getStartDay();
        $this->weekNumbers = $filter->getWeekNumbers();
        $this->withInputs = $filter->getWithInputs();
        $this->withConfirmation = $filter->getWithConfirmation();
        $this->unavailable = $filter->getUnavailable();
        $this->fixedWeeks = $filter->getFixedWeeks();
        
        // Range-specific properties
        if ($filter instanceof RangeFilter) {
            $this->withPresets = $filter->getWithPresets() ? true : null;
            $this->presets = $filter->getPresets();
            $this->minRange = $filter->getMinRange();
            $this->maxRange = $filter->getMaxRange();
        } else {
            $this->withPresets = null;
            $this->presets = null;
            $this->minRange = null;
            $this->maxRange = null;
        }
        
        // Convert false booleans to null so Blade doesn't add them
        $this->withToday = $this->withToday ?: null;
        $this->selectableHeader = $this->selectableHeader ?: null;
        $this->clearable = $this->clearable ?: null;
        $this->disabled = $this->disabled ?: null;
        $this->invalid = $this->invalid ?: null;
        $this->forceOpenTo = $this->forceOpenTo ?: null;
        $this->weekNumbers = $this->weekNumbers ?: null;
        $this->withInputs = $this->withInputs ?: null;
        $this->withConfirmation = $this->withConfirmation ?: null;
        $this->fixedWeeks = $this->fixedWeeks ?: null;
    }
    
    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('livewire-datatable::components.date-picker-filter');
    }
}
