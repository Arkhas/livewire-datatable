<?php

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

function createHasPaginationTestTable(): EloquentTable
{
    return new EloquentTable(TestModel::query());
}

test('it has default per page value', function () {
    $table = createHasPaginationTestTable();

    expect($table->getPerPage())->toBe(10);
});

test('it can set per page', function () {
    $table = createHasPaginationTestTable()
        ->perPage(25);

    expect($table->getPerPage())->toBe(25);
});

test('it has default per page options', function () {
    $table = createHasPaginationTestTable();

    expect($table->getPerPageOptions())->toBe([10, 25, 50, 100]);
});

test('it can set per page options', function () {
    $table = createHasPaginationTestTable()
        ->perPageOptions([5, 10, 20, 50]);

    expect($table->getPerPageOptions())->toBe([5, 10, 20, 50]);
});

test('it supports fluent pagination configuration', function () {
    $table = createHasPaginationTestTable()
        ->perPage(20)
        ->perPageOptions([20, 40, 60, 100]);

    expect($table->getPerPage())->toBe(20)
        ->and($table->getPerPageOptions())->toBe([20, 40, 60, 100]);
});
