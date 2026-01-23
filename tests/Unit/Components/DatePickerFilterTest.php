<?php

use Arkhas\LivewireDatatable\Components\DatePickerFilter;
use Arkhas\LivewireDatatable\Filters\DateFilter;
use Arkhas\LivewireDatatable\Filters\RangeFilter;
use Illuminate\Support\Carbon;

test('it can be created with DateFilter', function () {
    $filter = DateFilter::make('created_at')
        ->min('2024-01-01')
        ->max('2024-12-31')
        ->withToday()
        ->clearable();

    $component = new DatePickerFilter($filter, []);

    expect($component)
        ->toBeInstanceOf(DatePickerFilter::class)
        ->and($component->filter)->toBe($filter)
        ->and($component->dateMode)->toBe('single')
        ->and($component->min)->toBe('2024-01-01')
        ->and($component->max)->toBe('2024-12-31')
        ->and($component->withToday)->toBeTrue()
        ->and($component->clearable)->toBeTrue();
});

test('it can be created with RangeFilter', function () {
    $filter = RangeFilter::make('date_range')
        ->withPresets()
        ->presets('today yesterday thisWeek')
        ->minRange(3)
        ->maxRange(30);

    $component = new DatePickerFilter($filter, []);

    expect($component)
        ->toBeInstanceOf(DatePickerFilter::class)
        ->and($component->filter)->toBe($filter)
        ->and($component->dateMode)->toBe('range')
        ->and($component->withPresets)->toBeTrue()
        ->and($component->presets)->toBe('today yesterday thisWeek')
        ->and($component->minRange)->toBe(3)
        ->and($component->maxRange)->toBe(30);
});

test('it formats single date value', function () {
    $filter = DateFilter::make('created_at');
    $filters = ['created_at' => '2024-01-15'];

    $component = new DatePickerFilter($filter, $filters);

    expect($component->formattedValue)->toBe('2024-01-15');
});

test('it formats range date value from string', function () {
    $filter = RangeFilter::make('date_range');
    $filters = ['date_range' => ['2024-01-15/2024-01-17']];

    $component = new DatePickerFilter($filter, $filters);

    expect($component->formattedValue)->toBe('2024-01-15/2024-01-17');
});

test('it formats range date value from array', function () {
    $filter = RangeFilter::make('date_range');
    $filters = ['date_range' => ['start' => '2024-01-15', 'end' => '2024-01-17']];

    $component = new DatePickerFilter($filter, $filters);

    expect($component->formattedValue)->toBe('2024-01-15/2024-01-17');
});

test('it sets wire model correctly', function () {
    $filter = DateFilter::make('created_at');
    $component = new DatePickerFilter($filter, []);

    expect($component->wireModel)->toBe('filters.created_at');
});

test('it sets placeholder from filter label', function () {
    $filter = DateFilter::make('created_at')
        ->label('Created Date');

    $component = new DatePickerFilter($filter, []);

    expect($component->placeholder)->toBe('Created Date');
});

test('it sets placeholder from filter placeholder if set', function () {
    $filter = DateFilter::make('created_at')
        ->label('Created Date')
        ->placeholder('Select a date');

    $component = new DatePickerFilter($filter, []);

    expect($component->placeholder)->toBe('Select a date');
});

test('it converts false booleans to null for Blade', function () {
    $filter = DateFilter::make('created_at');

    $component = new DatePickerFilter($filter, []);

    expect($component->withToday)->toBeNull()
        ->and($component->clearable)->toBeNull()
        ->and($component->disabled)->toBeNull();
});

test('it keeps true booleans as true', function () {
    $filter = DateFilter::make('created_at')
        ->withToday()
        ->clearable();

    $component = new DatePickerFilter($filter, []);

    expect($component->withToday)->toBeTrue()
        ->and($component->clearable)->toBeTrue();
});

test('it sets range-specific properties to null for DateFilter', function () {
    $filter = DateFilter::make('created_at');

    $component = new DatePickerFilter($filter, []);

    expect($component->withPresets)->toBeNull()
        ->and($component->presets)->toBeNull()
        ->and($component->minRange)->toBeNull()
        ->and($component->maxRange)->toBeNull();
});

test('it can render view', function () {
    $filter = DateFilter::make('created_at');
    $component = new DatePickerFilter($filter, []);

    $view = $component->render();

    expect($view->getName())->toBe('livewire-datatable::components.date-picker-filter');
});

test('it handles null date value', function () {
    $filter = DateFilter::make('created_at');
    $filters = ['created_at' => null];

    $component = new DatePickerFilter($filter, $filters);

    expect($component->formattedValue)->toBeNull();
});

test('it handles empty date value', function () {
    $filter = DateFilter::make('created_at');
    $filters = ['created_at' => ''];

    $component = new DatePickerFilter($filter, $filters);

    expect($component->formattedValue)->toBeNull();
});

test('it handles invalid range format', function () {
    $filter = RangeFilter::make('date_range');
    $filters = ['date_range' => ['invalid']];

    $component = new DatePickerFilter($filter, $filters);

    expect($component->formattedValue)->toBeNull();
});
