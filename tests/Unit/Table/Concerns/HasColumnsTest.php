<?php

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Columns\Column;
use Arkhas\LivewireDatatable\Columns\ActionColumn;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

function createHasColumnsTestTable(): EloquentTable
{
    return new EloquentTable(TestModel::query());
}

test('it can set columns', function () {
    $columns = [
        Column::make('name'),
        Column::make('email'),
    ];

    $table = createHasColumnsTestTable()
        ->columns($columns);

    expect($table->getColumns())->toBe($columns);
});

test('it returns empty columns by default', function () {
    $table = createHasColumnsTestTable();

    expect($table->getColumns())->toBe([]);
});

test('it can get visible columns', function () {
    $table = createHasColumnsTestTable()
        ->columns([
            Column::make('name'),
            Column::make('email')->hidden(),
            Column::make('status'),
        ]);

    $visible = $table->getVisibleColumns();

    expect($visible)->toHaveCount(2);
});

test('it can get column by name', function () {
    $nameColumn = Column::make('name');
    $emailColumn = Column::make('email');

    $table = createHasColumnsTestTable()
        ->columns([$nameColumn, $emailColumn]);

    expect($table->getColumn('name'))->toBe($nameColumn)
        ->and($table->getColumn('email'))->toBe($emailColumn);
});

test('it returns null for missing column', function () {
    $table = createHasColumnsTestTable()
        ->columns([Column::make('name')]);

    expect($table->getColumn('email'))->toBeNull();
});

test('it can get sortable columns', function () {
    $table = createHasColumnsTestTable()
        ->columns([
            Column::make('name')->sortable(),
            Column::make('email')->sortable(false),
            Column::make('status')->sortable(),
        ]);

    $sortable = $table->getSortableColumns();

    expect($sortable)->toHaveCount(2);
});

test('it can get toggable columns', function () {
    $table = createHasColumnsTestTable()
        ->columns([
            Column::make('name')->toggable(),
            Column::make('email')->toggable(false),
            ActionColumn::make(), // Not toggable by default
        ]);

    $toggable = $table->getToggableColumns();

    expect($toggable)->toHaveCount(1);
});

test('it supports fluent columns', function () {
    $table = createHasColumnsTestTable()
        ->columns([
            Column::make('name')
                ->label('Full Name')
                ->sortable()
                ->toggable(),
        ]);

    $column = $table->getColumn('name');

    expect($column->getLabel())->toBe('Full Name')
        ->and($column->isSortable())->toBeTrue()
        ->and($column->isToggable())->toBeTrue();
});
