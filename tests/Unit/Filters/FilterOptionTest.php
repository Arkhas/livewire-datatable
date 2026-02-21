<?php

use Arkhas\LivewireDatatable\Filters\FilterOption;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

test('it can be created with make', function () {
    $option = FilterOption::make('active');

    expect($option)
        ->toBeInstanceOf(FilterOption::class)
        ->and($option->getName())->toBe('active');
});

test('it can be created with constructor', function () {
    $option = new FilterOption('inactive');

    expect($option->getName())->toBe('inactive');
});

test('it generates label from name if not set', function () {
    $option = FilterOption::make('is_active');

    expect($option->getLabel())->toBe('Is active');
});

test('it can set label', function () {
    $option = FilterOption::make('active')
        ->label('Active Users');

    expect($option->getLabel())->toBe('Active Users');
});

test('it can set icon', function () {
    $option = FilterOption::make('active')
        ->icon('circle-check');

    expect($option->getIcon())->toBe('circle-check');
});

test('it returns null icon by default', function () {
    $option = FilterOption::make('active');

    expect($option->getIcon())->toBeNull();
});

test('it can set count callback', function () {
    $option = FilterOption::make('active')
        ->count(fn() => 42);

    expect($option->getCount())->toBe(42);
});

test('it can set count directly with int', function () {
    $option = FilterOption::make('active')
        ->count(42);

    expect($option->getCount())->toBe(42);
});

test('it returns direct int count immediately', function () {
    $option = FilterOption::make('active')
        ->count(100);

    expect($option->getCount())->toBe(100);
});

test('it can use direct int count in toArray', function () {
    $option = FilterOption::make('active')
        ->label('Active')
        ->icon('check')
        ->count(25);

    $array = $option->toArray();

    expect($array['count'])->toBe(25);
});

test('it supports both int and closure count methods', function () {
    $optionWithInt = FilterOption::make('active')
        ->count(10);

    $optionWithClosure = FilterOption::make('inactive')
        ->count(fn() => 20);

    expect($optionWithInt->getCount())->toBe(10)
        ->and($optionWithClosure->getCount())->toBe(20);
});

test('it returns null count by default', function () {
    $option = FilterOption::make('active');

    expect($option->getCount())->toBeNull();
});

test('it can use dynamic count', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'User 3', 'email' => 'user3@example.com', 'status' => 'inactive']);

    $option = FilterOption::make('active')
        ->count(fn() => TestModel::where('status', 'active')->count());

    expect($option->getCount())->toBe(2);
});

test('it can set query callback', function () {
    $option = FilterOption::make('active')
        ->query(fn($query) => $query->where('status', 'active'));

    expect($option)->toBeInstanceOf(FilterOption::class);
});

test('it can apply query to builder', function () {
    TestModel::create(['name' => 'Active User', 'email' => 'active@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'Inactive User', 'email' => 'inactive@example.com', 'status' => 'inactive']);

    $option = FilterOption::make('active')
        ->query(fn($q) => $q->where('status', 'active'));

    $query = TestModel::query();
    $option->applyToQuery($query, 'active');

    // The option uses orWhere wrapping, so we need to test it properly
    expect($query->toSql())->toContain('status');
});

test('it does nothing when no query callback', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

    $option = FilterOption::make('active');

    $query = TestModel::query();
    $option->applyToQuery($query, 'active');

    expect($query->count())->toBe(2);
});

test('it can convert to array', function () {
    $option = FilterOption::make('active')
        ->label('Active')
        ->icon('check')
        ->count(fn() => 10);

    $array = $option->toArray();

    expect($array['name'])->toBe('active')
        ->and($array['label'])->toBe('Active')
        ->and($array['icon'])->toBe('check')
        ->and($array['count'])->toBe(10);
});

test('it converts to array with null count', function () {
    $option = FilterOption::make('active')
        ->label('Active');

    $array = $option->toArray();

    expect($array['count'])->toBeNull();
});

test('it supports fluent api', function () {
    $option = FilterOption::make('pending')
        ->label('Pending Review')
        ->icon('clock')
        ->count(fn() => 5)
        ->query(fn($q) => $q->where('status', 'pending'));

    expect($option)->toBeInstanceOf(FilterOption::class)
        ->and($option->getLabel())->toBe('Pending Review')
        ->and($option->getIcon())->toBe('clock')
        ->and($option->getCount())->toBe(5);
});
