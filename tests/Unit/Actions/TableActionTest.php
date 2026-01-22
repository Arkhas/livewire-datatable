<?php

use Arkhas\LivewireDatatable\Actions\TableAction;

test('it can be created with make', function () {
    $action = TableAction::make('delete');

    expect($action)
        ->toBeInstanceOf(TableAction::class)
        ->and($action->getName())->toBe('delete');
});

test('it can be created with constructor', function () {
    $action = new TableAction('export');

    expect($action->getName())->toBe('export');
});

test('it generates label from name if not set', function () {
    $action = TableAction::make('delete_all');

    expect($action->getLabel())->toBe('Delete all');
});

test('it can set label', function () {
    $action = TableAction::make('delete')
        ->label('Delete Selected');

    expect($action->getLabel())->toBe('Delete Selected');
});

test('it can set icon with default position', function () {
    $action = TableAction::make('delete')
        ->icon('trash');

    expect($action->getIcon())->toBe('trash')
        ->and($action->getIconPosition())->toBe('left');
});

test('it can set icon with custom position', function () {
    $action = TableAction::make('export')
        ->icon('download', 'right');

    expect($action->getIcon())->toBe('download')
        ->and($action->getIconPosition())->toBe('right');
});

test('it returns null icon by default', function () {
    $action = TableAction::make('delete');

    expect($action->getIcon())->toBeNull();
});

test('it returns left icon position by default', function () {
    $action = TableAction::make('delete');

    expect($action->getIconPosition())->toBe('left');
});

test('it can set props', function () {
    $action = TableAction::make('delete')
        ->props(['variant' => 'danger', 'size' => 'sm']);

    expect($action->getProps())->toBe(['variant' => 'danger', 'size' => 'sm']);
});

test('it returns empty props by default', function () {
    $action = TableAction::make('delete');

    expect($action->getProps())->toBe([]);
});

test('it can set styles', function () {
    $action = TableAction::make('delete')
        ->styles('color: red; font-weight: bold;');

    expect($action->getStyles())->toBe('color: red; font-weight: bold;');
});

test('it returns null styles by default', function () {
    $action = TableAction::make('delete');

    expect($action->getStyles())->toBeNull();
});

test('it can set handler', function () {
    $action = TableAction::make('delete')
        ->handle(fn($ids) => ['success' => true, 'deleted' => count($ids)]);

    expect($action)->toBeInstanceOf(TableAction::class);
});

test('it can execute action with handler', function () {
    $action = TableAction::make('delete')
        ->handle(fn($ids) => [
            'success' => true,
            'message' => 'Deleted ' . count($ids) . ' items',
        ]);

    $result = $action->execute([1, 2, 3]);

    expect($result['success'])->toBeTrue()
        ->and($result['message'])->toBe('Deleted 3 items');
});

test('it returns error when executing without handler', function () {
    $action = TableAction::make('delete');

    $result = $action->execute([1, 2, 3]);

    expect($result['success'])->toBeFalse()
        ->and($result['message'])->toBe('No handler defined');
});

test('it can set confirmation', function () {
    $action = TableAction::make('delete')
        ->confirm(fn($ids) => [
            'title' => 'Delete Items?',
            'message' => 'Are you sure you want to delete ' . count($ids) . ' items?',
        ]);

    expect($action->requiresConfirmation())->toBeTrue();
});

test('it does not require confirmation by default', function () {
    $action = TableAction::make('delete');

    expect($action->requiresConfirmation())->toBeFalse();
});

test('it can get confirmation data', function () {
    $action = TableAction::make('delete')
        ->confirm(fn($ids) => [
            'title' => 'Confirm Delete',
            'message' => 'Delete ' . count($ids) . ' selected items?',
        ]);

    $confirmation = $action->getConfirmation([1, 2, 3, 4, 5]);

    expect($confirmation['title'])->toBe('Confirm Delete')
        ->and($confirmation['message'])->toBe('Delete 5 selected items?');
});

test('it returns null confirmation when not set', function () {
    $action = TableAction::make('delete');

    expect($action->getConfirmation([1, 2, 3]))->toBeNull();
});

test('it can convert to array', function () {
    $action = TableAction::make('delete')
        ->label('Delete Selected')
        ->icon('trash', 'left')
        ->props(['variant' => 'danger'])
        ->styles('font-weight: bold;')
        ->confirm(fn($ids) => ['title' => 'Confirm']);

    $array = $action->toArray();

    expect($array['name'])->toBe('delete')
        ->and($array['label'])->toBe('Delete Selected')
        ->and($array['icon'])->toBe('trash')
        ->and($array['iconPosition'])->toBe('left')
        ->and($array['props'])->toBe(['variant' => 'danger'])
        ->and($array['styles'])->toBe('font-weight: bold;')
        ->and($array['requiresConfirmation'])->toBeTrue()
        ->and($array['type'])->toBe('action');
});

test('it supports fluent api', function () {
    $action = TableAction::make('bulk_delete')
        ->label('Delete All')
        ->icon('trash-2', 'right')
        ->props(['variant' => 'destructive'])
        ->styles('margin-left: 8px;')
        ->handle(fn($ids) => ['success' => true])
        ->confirm(fn($ids) => ['title' => 'Confirm']);

    expect($action)->toBeInstanceOf(TableAction::class)
        ->and($action->getLabel())->toBe('Delete All')
        ->and($action->getIcon())->toBe('trash-2')
        ->and($action->getIconPosition())->toBe('right')
        ->and($action->getProps())->toBe(['variant' => 'destructive'])
        ->and($action->getStyles())->toBe('margin-left: 8px;')
        ->and($action->requiresConfirmation())->toBeTrue();
});
