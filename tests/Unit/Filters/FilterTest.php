<?php

use Arkhas\LivewireDatatable\Filters\Filter;
use Arkhas\LivewireDatatable\Filters\FilterOption;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

test('it can be created with make', function () {
    $filter = Filter::make('status');

    expect($filter)
        ->toBeInstanceOf(Filter::class)
        ->and($filter->getName())->toBe('status');
});

test('it can be created with constructor', function () {
    $filter = new Filter('category');

    expect($filter->getName())->toBe('category');
});

test('it generates label from name if not set', function () {
    $filter = Filter::make('account_status');

    expect($filter->getLabel())->toBe('Account status');
});

test('it can set label', function () {
    $filter = Filter::make('status')
        ->label('Account Status');

    expect($filter->getLabel())->toBe('Account Status');
});

test('it is not multiple by default', function () {
    $filter = Filter::make('status');

    expect($filter->isMultiple())->toBeFalse();
});

test('it can enable multiple selection', function () {
    $filter = Filter::make('status')
        ->multiple();

    expect($filter->isMultiple())->toBeTrue();
});

test('it can disable multiple selection', function () {
    $filter = Filter::make('status')
        ->multiple()
        ->multiple(false);

    expect($filter->isMultiple())->toBeFalse();
});

test('it can set options', function () {
    $options = [
        FilterOption::make('active')->label('Active'),
        FilterOption::make('inactive')->label('Inactive'),
    ];

    $filter = Filter::make('status')
        ->options($options);

    expect($filter->getOptions())->toHaveCount(2)
        ->and($filter->getOptions())->toBe($options);
});

test('it returns empty options by default', function () {
    $filter = Filter::make('status');

    expect($filter->getOptions())->toBe([]);
});

test('it can get option by name', function () {
    $activeOption = FilterOption::make('active');
    $inactiveOption = FilterOption::make('inactive');

    $filter = Filter::make('status')
        ->options([$activeOption, $inactiveOption]);

    expect($filter->getOption('active'))->toBe($activeOption)
        ->and($filter->getOption('inactive'))->toBe($inactiveOption);
});

test('it returns null for missing option', function () {
    $filter = Filter::make('status')
        ->options([
            FilterOption::make('active'),
        ]);

    expect($filter->getOption('pending'))->toBeNull();
});

test('it can set global query callback', function () {
    $filter = Filter::make('status')
        ->query(fn($query, $values) => $query->whereIn('status', $values));

    expect($filter)->toBeInstanceOf(Filter::class);
});

test('it does not apply filter with empty values', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com', 'status' => 'inactive']);

    $filter = Filter::make('status')
        ->options([
            FilterOption::make('active')
                ->query(fn($q) => $q->where('status', 'active')),
        ]);

    $query = TestModel::query();
    $filter->applyToQuery($query, []);

    expect($query->count())->toBe(2);
});

test('it can apply option queries', function () {
    TestModel::create(['name' => 'Active User', 'email' => 'active@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'Inactive User', 'email' => 'inactive@example.com', 'status' => 'inactive']);

    $filter = Filter::make('status')
        ->options([
            FilterOption::make('active')
                ->query(fn($q) => $q->where('status', 'active')),
            FilterOption::make('inactive')
                ->query(fn($q) => $q->where('status', 'inactive')),
        ]);

    $query = TestModel::query();
    $filter->applyToQuery($query, ['active']);

    expect($query->count())->toBe(1)
        ->and($query->first()->name)->toBe('Active User');
});

test('it applies global query callback', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com', 'status' => 'inactive']);
    TestModel::create(['name' => 'User 3', 'email' => 'user3@example.com', 'status' => 'pending']);

    $filter = Filter::make('status')
        ->options([
            FilterOption::make('active'),
            FilterOption::make('inactive'),
        ])
        ->query(fn($query, $values) => $query->whereIn('status', $values));

    $query = TestModel::query();
    $filter->applyToQuery($query, ['active', 'inactive']);

    expect($query->count())->toBe(2);
});

test('it can get selected count', function () {
    $filter = Filter::make('status');

    expect($filter->getSelectedCount([]))->toBe(0)
        ->and($filter->getSelectedCount(['active']))->toBe(1)
        ->and($filter->getSelectedCount(['active', 'inactive', 'pending']))->toBe(3);
});

test('it can convert to array', function () {
    $filter = Filter::make('status')
        ->label('Status Filter')
        ->multiple()
        ->options([
            FilterOption::make('active')
                ->label('Active')
                ->icon('check'),
            FilterOption::make('inactive')
                ->label('Inactive')
                ->icon('x'),
        ]);

    $array = $filter->toArray();

    expect($array['name'])->toBe('status')
        ->and($array['label'])->toBe('Status Filter')
        ->and($array['multiple'])->toBeTrue()
        ->and($array['options'])->toHaveCount(2)
        ->and($array['options'][0]['name'])->toBe('active')
        ->and($array['options'][1]['name'])->toBe('inactive');
});

test('it supports fluent api', function () {
    $filter = Filter::make('category')
        ->label('Category')
        ->multiple()
        ->options([
            FilterOption::make('tech'),
            FilterOption::make('sport'),
        ])
        ->query(fn($q, $v) => $q);

    expect($filter)->toBeInstanceOf(Filter::class)
        ->and($filter->getLabel())->toBe('Category')
        ->and($filter->isMultiple())->toBeTrue()
        ->and($filter->getOptions())->toHaveCount(2);
});
