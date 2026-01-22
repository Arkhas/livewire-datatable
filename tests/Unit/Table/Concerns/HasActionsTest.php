<?php

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Actions\TableAction;
use Arkhas\LivewireDatatable\Actions\TableActionGroup;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

function createHasActionsTestTable(): EloquentTable
{
    return new EloquentTable(TestModel::query());
}

test('it can set actions', function () {
    $actions = [
        TableAction::make('delete'),
        TableAction::make('archive'),
    ];

    $table = createHasActionsTestTable()
        ->actions($actions);

    expect($table->getActions())->toBe($actions);
});

test('it returns empty actions by default', function () {
    $table = createHasActionsTestTable();

    expect($table->getActions())->toBe([]);
});

test('it can get action by name', function () {
    $deleteAction = TableAction::make('delete');
    $archiveAction = TableAction::make('archive');

    $table = createHasActionsTestTable()
        ->actions([$deleteAction, $archiveAction]);

    expect($table->getAction('delete'))->toBe($deleteAction)
        ->and($table->getAction('archive'))->toBe($archiveAction);
});

test('it returns null for missing action', function () {
    $table = createHasActionsTestTable()
        ->actions([TableAction::make('delete')]);

    expect($table->getAction('archive'))->toBeNull();
});

test('it can find action in group', function () {
    $nestedAction = TableAction::make('nested_delete')
        ->handle(fn($ids) => ['success' => true]);

    $table = createHasActionsTestTable()
        ->actions([
            TableAction::make('delete'),
            TableActionGroup::make('more')
                ->actions([
                    $nestedAction,
                    TableAction::make('archive'),
                ]),
        ]);

    expect($table->getAction('nested_delete'))->toBe($nestedAction);
});

test('it can execute action', function () {
    $table = createHasActionsTestTable()
        ->actions([
            TableAction::make('delete')
                ->handle(fn($ids) => [
                    'success' => true,
                    'count' => count($ids),
                ]),
        ]);

    $result = $table->executeAction('delete', [1, 2, 3]);

    expect($result['success'])->toBeTrue()
        ->and($result['count'])->toBe(3);
});

test('it returns error for missing action execution', function () {
    $table = createHasActionsTestTable()
        ->actions([TableAction::make('delete')]);

    $result = $table->executeAction('unknown', [1, 2, 3]);

    expect($result['success'])->toBeFalse()
        ->and($result['message'])->toBe('Action not found');
});

test('it can execute nested action', function () {
    $table = createHasActionsTestTable()
        ->actions([
            TableActionGroup::make('more')
                ->actions([
                    TableAction::make('archive')
                        ->handle(fn($ids) => ['success' => true, 'archived' => count($ids)]),
                ]),
        ]);

    $result = $table->executeAction('archive', [1, 2]);

    expect($result['success'])->toBeTrue()
        ->and($result['archived'])->toBe(2);
});
