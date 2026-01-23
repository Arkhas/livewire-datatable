<?php

use Arkhas\LivewireDatatable\Filters\DateFilter;
use Arkhas\LivewireDatatable\Filters\DatePickerFilter;
use Arkhas\LivewireDatatable\Filters\RangeFilter;

test('it defaults column to filter name', function () {
    $filter = DateFilter::make('created_at');

    expect($filter->getColumn())->toBe('created_at');
});

test('it can set and get column', function () {
    $filter = DateFilter::make('created_at')
        ->column('updated_at');

    expect($filter->getColumn())->toBe('updated_at');
});

test('it can set all date picker properties', function () {
    $filter = DateFilter::make('created_at')
        ->min('2024-01-01')
        ->max('2024-12-31')
        ->withToday()
        ->selectableHeader()
        ->clearable()
        ->disabled()
        ->invalid()
        ->locale('fr')
        ->placeholder('Select date')
        ->openTo('2024-06-15')
        ->forceOpenTo()
        ->months(2)
        ->startDay(1)
        ->weekNumbers()
        ->withInputs()
        ->withConfirmation()
        ->unavailable('2024-12-25,2024-12-31')
        ->fixedWeeks();

    expect($filter->getMin())->toBe('2024-01-01')
        ->and($filter->getMax())->toBe('2024-12-31')
        ->and($filter->getWithToday())->toBeTrue()
        ->and($filter->getSelectableHeader())->toBeTrue()
        ->and($filter->getClearable())->toBeTrue()
        ->and($filter->getDisabled())->toBeTrue()
        ->and($filter->getInvalid())->toBeTrue()
        ->and($filter->getLocale())->toBe('fr')
        ->and($filter->getPlaceholder())->toBe('Select date')
        ->and($filter->getOpenTo())->toBe('2024-06-15')
        ->and($filter->getForceOpenTo())->toBeTrue()
        ->and($filter->getMonths())->toBe(2)
        ->and($filter->getStartDay())->toBe('1')
        ->and($filter->getWeekNumbers())->toBeTrue()
        ->and($filter->getWithInputs())->toBeTrue()
        ->and($filter->getWithConfirmation())->toBeTrue()
        ->and($filter->getUnavailable())->toBe('2024-12-25,2024-12-31')
        ->and($filter->getFixedWeeks())->toBeTrue();
});

test('it can disable boolean properties', function () {
    $filter = DateFilter::make('created_at')
        ->withToday()
        ->withToday(false)
        ->clearable()
        ->clearable(false);

    expect($filter->getWithToday())->toBeFalse()
        ->and($filter->getClearable())->toBeFalse();
});

test('it returns common attributes in toArray', function () {
    $filter = DateFilter::make('created_at')
        ->min('2024-01-01')
        ->max('2024-12-31')
        ->withToday()
        ->clearable();

    $array = $filter->toArray();

    expect($array['min'])->toBe('2024-01-01')
        ->and($array['max'])->toBe('2024-12-31')
        ->and($array['withToday'])->toBeTrue()
        ->and($array['clearable'])->toBeTrue();
});
