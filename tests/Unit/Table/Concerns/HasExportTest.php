<?php

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

function createHasExportTestTable(): EloquentTable
{
    return new EloquentTable(TestModel::query());
}

test('it is exportable by default', function () {
    $table = createHasExportTestTable();

    expect($table->isExportable())->toBeTrue();
});

test('it can disable export', function () {
    $table = createHasExportTestTable()
        ->exportable(false);

    expect($table->isExportable())->toBeFalse();
});

test('it can enable export', function () {
    $table = createHasExportTestTable()
        ->exportable(false)
        ->exportable(true);

    expect($table->isExportable())->toBeTrue();
});

test('it has default export formats', function () {
    $table = createHasExportTestTable();

    expect($table->getExportFormats())->toBe(['csv', 'xlsx']);
});

test('it can set export formats', function () {
    $table = createHasExportTestTable()
        ->exportFormats(['csv', 'pdf']);

    expect($table->getExportFormats())->toBe(['csv', 'pdf']);
});

test('it supports fluent export configuration', function () {
    $table = createHasExportTestTable()
        ->exportable()
        ->exportFormats(['csv', 'xlsx', 'pdf']);

    expect($table->isExportable())->toBeTrue()
        ->and($table->getExportFormats())->toBe(['csv', 'xlsx', 'pdf']);
});
