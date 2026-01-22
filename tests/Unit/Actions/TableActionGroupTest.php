<?php

use Arkhas\LivewireDatatable\Actions\TableAction;
use Arkhas\LivewireDatatable\Actions\TableActionGroup;

test('it can be created with make', function () {
    $group = TableActionGroup::make('bulk_actions');

    expect($group)
        ->toBeInstanceOf(TableActionGroup::class)
        ->and($group->getName())->toBe('bulk_actions');
});

test('it can be created with constructor', function () {
    $group = new TableActionGroup('actions');

    expect($group->getName())->toBe('actions');
});

test('it generates label from name if not set', function () {
    $group = TableActionGroup::make('bulk_actions');

    expect($group->getLabel())->toBe('Bulk actions');
});

test('it can set label', function () {
    $group = TableActionGroup::make('actions')
        ->label('More Actions');

    expect($group->getLabel())->toBe('More Actions');
});

test('it can set icon', function () {
    $group = TableActionGroup::make('actions')
        ->icon('chevron-down');

    expect($group->getIcon())->toBe('chevron-down');
});

test('it returns null icon by default', function () {
    $group = TableActionGroup::make('actions');

    expect($group->getIcon())->toBeNull();
});

test('it can set props', function () {
    $group = TableActionGroup::make('actions')
        ->props(['variant' => 'outline', 'size' => 'sm']);

    expect($group->getProps())->toBe(['variant' => 'outline', 'size' => 'sm']);
});

test('it returns empty props by default', function () {
    $group = TableActionGroup::make('actions');

    expect($group->getProps())->toBe([]);
});

test('it can set styles', function () {
    $group = TableActionGroup::make('actions')
        ->styles('min-width: 150px;');

    expect($group->getStyles())->toBe('min-width: 150px;');
});

test('it returns null styles by default', function () {
    $group = TableActionGroup::make('actions');

    expect($group->getStyles())->toBeNull();
});

test('it can set actions', function () {
    $actions = [
        TableAction::make('delete'),
        TableAction::make('archive'),
    ];

    $group = TableActionGroup::make('bulk')
        ->actions($actions);

    expect($group->getActions())->toHaveCount(2)
        ->and($group->getActions())->toBe($actions);
});

test('it returns empty actions by default', function () {
    $group = TableActionGroup::make('actions');

    expect($group->getActions())->toBe([]);
});

test('it can get action by name', function () {
    $deleteAction = TableAction::make('delete');
    $archiveAction = TableAction::make('archive');

    $group = TableActionGroup::make('bulk')
        ->actions([$deleteAction, $archiveAction]);

    expect($group->getAction('delete'))->toBe($deleteAction)
        ->and($group->getAction('archive'))->toBe($archiveAction);
});

test('it returns null for missing action', function () {
    $group = TableActionGroup::make('bulk')
        ->actions([
            TableAction::make('delete'),
        ]);

    expect($group->getAction('archive'))->toBeNull();
});

test('it returns error when executing directly', function () {
    $group = TableActionGroup::make('bulk')
        ->actions([
            TableAction::make('delete')
                ->handle(fn($ids) => ['success' => true]),
        ]);

    $result = $group->execute([1, 2, 3]);

    expect($result['success'])->toBeFalse()
        ->and($result['message'])->toBe('Cannot execute action group directly');
});

test('it does not require confirmation', function () {
    $group = TableActionGroup::make('bulk');

    expect($group->requiresConfirmation())->toBeFalse();
});

test('it can convert to array', function () {
    $group = TableActionGroup::make('bulk_actions')
        ->label('Bulk Actions')
        ->icon('more-horizontal')
        ->props(['variant' => 'outline'])
        ->styles('margin-right: 4px;')
        ->actions([
            TableAction::make('delete')
                ->label('Delete Selected'),
            TableAction::make('archive')
                ->label('Archive Selected'),
        ]);

    $array = $group->toArray();

    expect($array['name'])->toBe('bulk_actions')
        ->and($array['label'])->toBe('Bulk Actions')
        ->and($array['icon'])->toBe('more-horizontal')
        ->and($array['props'])->toBe(['variant' => 'outline'])
        ->and($array['styles'])->toBe('margin-right: 4px;')
        ->and($array['type'])->toBe('group')
        ->and($array['actions'])->toHaveCount(2)
        ->and($array['actions'][0]['name'])->toBe('delete')
        ->and($array['actions'][1]['name'])->toBe('archive');
});

test('it supports fluent api', function () {
    $group = TableActionGroup::make('actions')
        ->label('Actions')
        ->icon('menu')
        ->props(['size' => 'sm'])
        ->styles('gap: 4px;')
        ->actions([
            TableAction::make('edit'),
            TableAction::make('delete'),
        ]);

    expect($group)->toBeInstanceOf(TableActionGroup::class)
        ->and($group->getLabel())->toBe('Actions')
        ->and($group->getIcon())->toBe('menu')
        ->and($group->getProps())->toBe(['size' => 'sm'])
        ->and($group->getStyles())->toBe('gap: 4px;')
        ->and($group->getActions())->toHaveCount(2);
});
