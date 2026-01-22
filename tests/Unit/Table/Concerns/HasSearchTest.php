<?php

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

function createHasSearchTestTable(): EloquentTable
{
    return new EloquentTable(TestModel::query());
}

test('it is searchable by default', function () {
    $table = createHasSearchTestTable();

    expect($table->isSearchable())->toBeTrue();
});

test('it can enable searchable with columns', function () {
    $table = createHasSearchTestTable()
        ->searchable(['name', 'email']);

    expect($table->isSearchable())->toBeTrue()
        ->and($table->getSearchColumns())->toBe(['name', 'email']);
});

test('it can enable searchable without columns', function () {
    $table = createHasSearchTestTable()
        ->searchable();

    expect($table->isSearchable())->toBeTrue()
        ->and($table->getSearchColumns())->toBe([]);
});

test('it can disable search', function () {
    $table = createHasSearchTestTable()
        ->notSearchable();

    expect($table->isSearchable())->toBeFalse();
});

test('it should search from columns by default', function () {
    $table = createHasSearchTestTable();

    expect($table->shouldSearchFromColumns())->toBeTrue();
});

test('it should not search from columns when explicit columns set', function () {
    $table = createHasSearchTestTable()
        ->searchable(['name']);

    expect($table->shouldSearchFromColumns())->toBeFalse();
});

test('it has default search placeholder', function () {
    $table = createHasSearchTestTable();

    expect($table->getSearchPlaceholder())->toBe('Search...');
});

test('it can set search placeholder', function () {
    $table = createHasSearchTestTable()
        ->searchPlaceholder('Search users...');

    expect($table->getSearchPlaceholder())->toBe('Search users...');
});

test('it returns empty search columns by default', function () {
    $table = createHasSearchTestTable();

    expect($table->getSearchColumns())->toBe([]);
});

test('it supports fluent search configuration', function () {
    $table = createHasSearchTestTable()
        ->searchable(['name', 'email'])
        ->searchPlaceholder('Find users...');

    expect($table->isSearchable())->toBeTrue()
        ->and($table->getSearchColumns())->toBe(['name', 'email'])
        ->and($table->getSearchPlaceholder())->toBe('Find users...');
});
