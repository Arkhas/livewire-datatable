<?php

use Arkhas\LivewireDatatable\Actions\ColumnAction;
use Arkhas\LivewireDatatable\Actions\ColumnActionGroup;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

test('it can be created with make', function () {
    $group = ColumnActionGroup::make();

    expect($group)->toBeInstanceOf(ColumnActionGroup::class);
});

test('it can set icon', function () {
    $group = ColumnActionGroup::make()
        ->icon('ellipsis-vertical');

    expect($group->getIcon())->toBe('ellipsis-vertical');
});

test('it returns null icon by default', function () {
    $group = ColumnActionGroup::make();

    expect($group->getIcon())->toBeNull();
});

test('it can set actions', function () {
    $actions = [
        ColumnAction::make('edit'),
        ColumnAction::make('delete'),
    ];

    $group = ColumnActionGroup::make()
        ->actions($actions);

    expect($group->getActions())->toHaveCount(2)
        ->and($group->getActions())->toBe($actions);
});

test('it returns empty actions by default', function () {
    $group = ColumnActionGroup::make();

    expect($group->getActions())->toBe([]);
});

test('it can get action by name', function () {
    $editAction = ColumnAction::make('edit');
    $deleteAction = ColumnAction::make('delete');

    $group = ColumnActionGroup::make()
        ->actions([$editAction, $deleteAction]);

    expect($group->getAction('edit'))->toBe($editAction)
        ->and($group->getAction('delete'))->toBe($deleteAction);
});

test('it returns null for missing action', function () {
    $group = ColumnActionGroup::make()
        ->actions([
            ColumnAction::make('edit'),
        ]);

    expect($group->getAction('delete'))->toBeNull();
});

test('it can convert to array for model', function () {
    $model = TestModel::create([
        'name' => 'Test',
        'email' => 'test@example.com',
    ]);

    $group = ColumnActionGroup::make()
        ->icon('dots')
        ->actions([
            ColumnAction::make('edit')
                ->label(fn($model) => "Edit {$model->name}"),
            ColumnAction::make('delete')
                ->label('Delete'),
        ]);

    $array = $group->toArrayForModel($model);

    expect($array['icon'])->toBe('dots')
        ->and($array['type'])->toBe('group')
        ->and($array['actions'])->toHaveCount(2)
        ->and($array['actions'][0]['label'])->toBe('Edit Test')
        ->and($array['actions'][1]['label'])->toBe('Delete');
});

test('it can convert to array', function () {
    $group = ColumnActionGroup::make()
        ->icon('menu')
        ->actions([
            ColumnAction::make('edit')
                ->label('Edit'),
            ColumnAction::make('delete')
                ->label('Delete'),
        ]);

    $array = $group->toArray();

    expect($array['icon'])->toBe('menu')
        ->and($array['type'])->toBe('group')
        ->and($array['actions'])->toHaveCount(2)
        ->and($array['actions'][0]['name'])->toBe('edit')
        ->and($array['actions'][1]['name'])->toBe('delete');
});

test('it supports fluent api', function () {
    $group = ColumnActionGroup::make()
        ->icon('more')
        ->actions([
            ColumnAction::make('view'),
            ColumnAction::make('edit'),
            ColumnAction::make('delete'),
        ]);

    expect($group)->toBeInstanceOf(ColumnActionGroup::class)
        ->and($group->getIcon())->toBe('more')
        ->and($group->getActions())->toHaveCount(3);
});
