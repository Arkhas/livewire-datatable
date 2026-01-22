<?php

use Arkhas\LivewireDatatable\Actions\ColumnAction;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

test('it can be created with make', function () {
    $action = ColumnAction::make('edit');

    expect($action)
        ->toBeInstanceOf(ColumnAction::class)
        ->and($action->getName())->toBe('edit');
});

test('it can be created with constructor', function () {
    $action = new ColumnAction('delete');

    expect($action->getName())->toBe('delete');
});

test('it generates label from name if not set', function () {
    $action = ColumnAction::make('edit_item');

    expect($action->getLabel())->toBe('Edit item');
});

test('it can set string label', function () {
    $action = ColumnAction::make('edit')
        ->label('Edit Record');

    expect($action->getLabel())->toBe('Edit Record');
});

test('it can set closure label', function () {
    $model = TestModel::create([
        'name' => 'John',
        'email' => 'john@example.com',
    ]);

    $action = ColumnAction::make('edit')
        ->label(fn($model) => "Edit {$model->name}");

    expect($action->getLabel($model))->toBe('Edit John');
});

test('it returns closure label in to array', function () {
    $action = ColumnAction::make('edit_item')
        ->label(fn($model) => "Edit {$model->name}");

    // When label is a closure, toArray() returns the name
    $array = $action->toArray();
    expect($array['label'])->toBe('edit_item');
});

test('it can set string icon', function () {
    $action = ColumnAction::make('edit')
        ->icon('pencil');

    expect($action->getIcon())->toBe('pencil');
});

test('it can set closure icon', function () {
    $model = TestModel::create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'status' => 'active',
    ]);

    $action = ColumnAction::make('toggle')
        ->icon(fn($model) => $model->status === 'active' ? 'pause' : 'play');

    expect($action->getIcon($model))->toBe('pause');
});

test('it returns string icon without model', function () {
    $action = ColumnAction::make('edit')
        ->icon('pencil');

    expect($action->getIcon())->toBe('pencil');
});

test('it returns null icon by default', function () {
    $action = ColumnAction::make('edit');

    expect($action->getIcon())->toBeNull();
});

test('it can set string url', function () {
    $action = ColumnAction::make('view')
        ->url('/items');

    expect($action->getUrl())->toBe('/items')
        ->and($action->hasUrl())->toBeTrue();
});

test('it can set closure url', function () {
    $model = TestModel::create([
        'name' => 'Test',
        'email' => 'test@example.com',
    ]);

    $action = ColumnAction::make('edit')
        ->url(fn($model) => "/items/{$model->id}/edit");

    expect($action->getUrl($model))->toBe("/items/{$model->id}/edit");
});

test('it does not have url by default', function () {
    $action = ColumnAction::make('edit');

    expect($action->hasUrl())->toBeFalse()
        ->and($action->getUrl())->toBeNull();
});

test('it can set props', function () {
    $action = ColumnAction::make('edit')
        ->props(['variant' => 'ghost', 'size' => 'sm']);

    expect($action->getProps())->toBe(['variant' => 'ghost', 'size' => 'sm']);
});

test('it returns empty props by default', function () {
    $action = ColumnAction::make('edit');

    expect($action->getProps())->toBe([]);
});

test('it can set separator', function () {
    $action = ColumnAction::make('edit')
        ->separator();

    expect($action->hasSeparator())->toBeTrue();
});

test('it does not have separator by default', function () {
    $action = ColumnAction::make('edit');

    expect($action->hasSeparator())->toBeFalse();
});

test('it can disable separator', function () {
    $action = ColumnAction::make('edit')
        ->separator()
        ->separator(false);

    expect($action->hasSeparator())->toBeFalse();
});

test('it can set handler', function () {
    $action = ColumnAction::make('delete')
        ->handle(fn($model) => ['success' => true, 'message' => 'Deleted']);

    expect($action->hasHandler())->toBeTrue();
});

test('it does not have handler by default', function () {
    $action = ColumnAction::make('edit');

    expect($action->hasHandler())->toBeFalse();
});

test('it can execute action with handler', function () {
    $model = TestModel::create([
        'name' => 'Test',
        'email' => 'test@example.com',
    ]);

    $action = ColumnAction::make('delete')
        ->handle(fn($model) => [
            'success' => true,
            'message' => "Deleted {$model->name}",
        ]);

    $result = $action->execute($model);

    expect($result['success'])->toBeTrue()
        ->and($result['message'])->toBe('Deleted Test');
});

test('it returns error when executing without handler', function () {
    $model = TestModel::create([
        'name' => 'Test',
        'email' => 'test@example.com',
    ]);

    $action = ColumnAction::make('edit');

    $result = $action->execute($model);

    expect($result['success'])->toBeFalse()
        ->and($result['message'])->toBe('No handler defined');
});

test('it can set confirmation', function () {
    $action = ColumnAction::make('delete')
        ->confirm(fn($model) => [
            'title' => 'Delete?',
            'message' => "Are you sure you want to delete {$model->name}?",
        ]);

    expect($action->requiresConfirmation())->toBeTrue();
});

test('it does not require confirmation by default', function () {
    $action = ColumnAction::make('edit');

    expect($action->requiresConfirmation())->toBeFalse();
});

test('it can get confirmation data', function () {
    $model = TestModel::create([
        'name' => 'Test Item',
        'email' => 'test@example.com',
    ]);

    $action = ColumnAction::make('delete')
        ->confirm(fn($model) => [
            'title' => 'Confirm Delete',
            'message' => "Delete {$model->name}?",
        ]);

    $confirmation = $action->getConfirmation($model);

    expect($confirmation['title'])->toBe('Confirm Delete')
        ->and($confirmation['message'])->toBe('Delete Test Item?');
});

test('it returns null confirmation when not set', function () {
    $model = TestModel::create([
        'name' => 'Test',
        'email' => 'test@example.com',
    ]);

    $action = ColumnAction::make('edit');

    expect($action->getConfirmation($model))->toBeNull();
});

test('it can convert to array for model', function () {
    $model = TestModel::create([
        'name' => 'Test',
        'email' => 'test@example.com',
    ]);

    $action = ColumnAction::make('edit')
        ->label(fn($model) => "Edit {$model->name}")
        ->icon('pencil')
        ->url(fn($model) => "/items/{$model->id}/edit")
        ->props(['variant' => 'ghost'])
        ->separator()
        ->handle(fn($model) => ['success' => true])
        ->confirm(fn($model) => ['title' => 'Confirm']);

    $array = $action->toArrayForModel($model);

    expect($array['name'])->toBe('edit')
        ->and($array['label'])->toBe('Edit Test')
        ->and($array['icon'])->toBe('pencil')
        ->and($array['url'])->toBe("/items/{$model->id}/edit")
        ->and($array['props'])->toBe(['variant' => 'ghost'])
        ->and($array['separator'])->toBeTrue()
        ->and($array['hasHandler'])->toBeTrue()
        ->and($array['requiresConfirmation'])->toBeTrue()
        ->and($array['type'])->toBe('action');
});

test('it can convert to array without model', function () {
    $action = ColumnAction::make('edit')
        ->label('Edit Item')
        ->icon('pencil')
        ->url('/items/edit')
        ->props(['variant' => 'ghost'])
        ->separator()
        ->handle(fn($model) => ['success' => true]);

    $array = $action->toArray();

    expect($array['name'])->toBe('edit')
        ->and($array['label'])->toBe('Edit Item')
        ->and($array['icon'])->toBe('pencil')
        ->and($array['url'])->toBe('/items/edit')
        ->and($array['props'])->toBe(['variant' => 'ghost'])
        ->and($array['separator'])->toBeTrue()
        ->and($array['hasHandler'])->toBeTrue()
        ->and($array['requiresConfirmation'])->toBeFalse()
        ->and($array['type'])->toBe('action');
});

test('it returns null for closure icon in to array', function () {
    $action = ColumnAction::make('edit')
        ->icon(fn($model) => 'pencil');

    $array = $action->toArray();

    expect($array['icon'])->toBeNull();
});

test('it returns null for closure url in to array', function () {
    $action = ColumnAction::make('edit')
        ->url(fn($model) => "/items/{$model->id}");

    $array = $action->toArray();

    expect($array['url'])->toBeNull();
});

test('it supports fluent api', function () {
    $action = ColumnAction::make('delete')
        ->label('Delete')
        ->icon('trash')
        ->url('/delete')
        ->props(['variant' => 'danger'])
        ->separator()
        ->handle(fn($m) => ['success' => true])
        ->confirm(fn($m) => ['title' => 'Confirm']);

    expect($action)->toBeInstanceOf(ColumnAction::class)
        ->and($action->getLabel())->toBe('Delete')
        ->and($action->getIcon())->toBe('trash')
        ->and($action->getUrl())->toBe('/delete')
        ->and($action->getProps())->toBe(['variant' => 'danger'])
        ->and($action->hasSeparator())->toBeTrue()
        ->and($action->hasHandler())->toBeTrue()
        ->and($action->requiresConfirmation())->toBeTrue();
});
