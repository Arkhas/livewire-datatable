<?php

use Arkhas\LivewireDatatable\Columns\ActionColumn;
use Arkhas\LivewireDatatable\Actions\ColumnAction;
use Arkhas\LivewireDatatable\Actions\ColumnActionGroup;

test('it can be created with make', function () {
    $column = ActionColumn::make();

    expect($column)->toBeInstanceOf(ActionColumn::class);
});

test('it has default name', function () {
    $column = ActionColumn::make();

    expect($column->getName())->toBe('__actions');
});

test('it can have custom name', function () {
    $column = ActionColumn::make('custom_actions');

    expect($column->getName())->toBe('custom_actions');
});

test('it is not sortable by default', function () {
    $column = ActionColumn::make();

    expect($column->isSortable())->toBeFalse();
});

test('it is not toggable by default', function () {
    $column = ActionColumn::make();

    expect($column->isToggable())->toBeFalse();
});

test('it has empty label by default', function () {
    $column = ActionColumn::make();

    expect($column->getLabel())->toBe('');
});

test('it can set column action', function () {
    $action = ColumnAction::make('edit')
        ->label('Edit');

    $column = ActionColumn::make()
        ->action($action);

    expect($column->getAction())->toBe($action);
});

test('it can set column action group', function () {
    $group = ColumnActionGroup::make()
        ->actions([
            ColumnAction::make('edit'),
            ColumnAction::make('delete'),
        ]);

    $column = ActionColumn::make()
        ->action($group);

    expect($column->getAction())->toBe($group);
});

test('it returns null action by default', function () {
    $column = ActionColumn::make();

    expect($column->getAction())->toBeNull();
});

test('it can be converted to array', function () {
    $action = ColumnAction::make('edit')
        ->label('Edit');

    $column = ActionColumn::make()
        ->action($action);

    $array = $column->toArray();

    expect($array['type'])->toBe('action')
        ->and($array)->toHaveKey('action')
        ->and($array['action']['name'])->toBe('edit');
});

test('it converts to array with null action', function () {
    $column = ActionColumn::make();

    $array = $column->toArray();

    expect($array['type'])->toBe('action')
        ->and($array['action'])->toBeNull();
});

test('it inherits column methods', function () {
    $column = ActionColumn::make()
        ->width('50px')
        ->hidden();

    expect($column->getWidth())->toBe('50px')
        ->and($column->isHidden())->toBeTrue();
});
