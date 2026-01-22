<?php

use Arkhas\LivewireDatatable\Columns\CheckboxColumn;

test('it can be created with make', function () {
    $column = CheckboxColumn::make();

    expect($column)->toBeInstanceOf(CheckboxColumn::class);
});

test('it has default name', function () {
    $column = CheckboxColumn::make();

    expect($column->getName())->toBe('__checkbox');
});

test('it ignores custom name in make', function () {
    $column = CheckboxColumn::make('custom');

    // CheckboxColumn always uses __checkbox internally
    expect($column->getName())->toBe('__checkbox');
});

test('it is not sortable by default', function () {
    $column = CheckboxColumn::make();

    expect($column->isSortable())->toBeFalse();
});

test('it is not toggable by default', function () {
    $column = CheckboxColumn::make();

    expect($column->isToggable())->toBeFalse();
});

test('it has empty label by default', function () {
    $column = CheckboxColumn::make();

    expect($column->getLabel())->toBe('');
});

test('it can be converted to array', function () {
    $column = CheckboxColumn::make();

    $array = $column->toArray();

    expect($array['type'])->toBe('checkbox')
        ->and($array['name'])->toBe('__checkbox')
        ->and($array['label'])->toBe('')
        ->and($array['sortable'])->toBeFalse()
        ->and($array['toggable'])->toBeFalse();
});

test('it inherits column methods', function () {
    $column = CheckboxColumn::make()
        ->width('40px')
        ->hidden();

    expect($column->getWidth())->toBe('40px')
        ->and($column->isHidden())->toBeTrue();
});
