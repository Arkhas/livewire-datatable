<?php

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Filters\Filter;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

function createHasFiltersTestTable(): EloquentTable
{
    return new EloquentTable(TestModel::query());
}

test('it can set filters', function () {
    $filters = [
        Filter::make('status'),
        Filter::make('category'),
    ];

    $table = createHasFiltersTestTable()
        ->filters($filters);

    expect($table->getFilters())->toBe($filters);
});

test('it returns empty filters by default', function () {
    $table = createHasFiltersTestTable();

    expect($table->getFilters())->toBe([]);
});

test('it can get filter by name', function () {
    $statusFilter = Filter::make('status');
    $categoryFilter = Filter::make('category');

    $table = createHasFiltersTestTable()
        ->filters([$statusFilter, $categoryFilter]);

    expect($table->getFilter('status'))->toBe($statusFilter)
        ->and($table->getFilter('category'))->toBe($categoryFilter);
});

test('it returns null for missing filter', function () {
    $table = createHasFiltersTestTable()
        ->filters([Filter::make('status')]);

    expect($table->getFilter('category'))->toBeNull();
});

test('it can count active filters', function () {
    $table = createHasFiltersTestTable()
        ->filters([
            Filter::make('status'),
            Filter::make('category'),
        ]);

    expect($table->getActiveFiltersCount([]))->toBe(0)
        ->and($table->getActiveFiltersCount(['status' => ['active']]))->toBe(1)
        ->and($table->getActiveFiltersCount([
            'status' => ['active'],
            'category' => ['tech', 'sport'],
        ]))->toBe(2);
});

test('it ignores empty filter values in count', function () {
    $table = createHasFiltersTestTable()
        ->filters([
            Filter::make('status'),
            Filter::make('category'),
        ]);

    $count = $table->getActiveFiltersCount([
        'status' => ['active'],
        'category' => [],
    ]);

    expect($count)->toBe(1);
});
