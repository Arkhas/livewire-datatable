<?php

use Arkhas\LivewireDatatable\Filters\DateFilter;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;
use Illuminate\Support\Carbon;

beforeEach(function () {
    TestModel::query()->delete();
});

test('it can be created with make', function () {
    $filter = DateFilter::make('created_at');

    expect($filter)
        ->toBeInstanceOf(DateFilter::class)
        ->and($filter->getName())->toBe('created_at');
    
    $array = $filter->toArray();
    expect($array['type'])->toBe('date');
});

test('it returns single mode', function () {
    $filter = DateFilter::make('created_at');

    expect($filter->getMode())->toBe('single');
});

test('it can set column', function () {
    $filter = DateFilter::make('created_at')
        ->column('updated_at');

    expect($filter->getColumn())->toBe('updated_at');
});

test('it defaults column to filter name', function () {
    $filter = DateFilter::make('created_at');

    expect($filter->getColumn())->toBe('created_at');
});

test('it can set date picker properties', function () {
    $filter = DateFilter::make('created_at')
        ->min('2024-01-01')
        ->max('2024-12-31')
        ->withToday()
        ->clearable()
        ->locale('fr');

    expect($filter->getMin())->toBe('2024-01-01')
        ->and($filter->getMax())->toBe('2024-12-31')
        ->and($filter->getWithToday())->toBeTrue()
        ->and($filter->getClearable())->toBeTrue()
        ->and($filter->getLocale())->toBe('fr');
});

test('it does not apply filter with empty values', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com', 'created_at' => now()]);

    $filter = DateFilter::make('created_at');

    $query = TestModel::query();
    $filter->applyToQuery($query, []);

    expect($query->count())->toBe(1);
});

test('it applies date filter with string date', function () {
    $targetDate = '2024-01-15';
    $otherDate = '2024-01-16';
    
    $user1 = TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    $user1->created_at = $targetDate;
    $user1->updated_at = $targetDate;
    $user1->save();
    
    $user2 = TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);
    $user2->created_at = $otherDate;
    $user2->updated_at = $otherDate;
    $user2->save();

    $filter = DateFilter::make('created_at')
        ->column('created_at');

    $query = TestModel::query();
    $filter->applyToQuery($query, $targetDate);

    $results = $query->get();
    expect($query->count())->toBe(1)
        ->and($results->first()->id)->toBe($user1->id);
});

test('it applies date filter with Carbon instance passed directly', function () {
    $targetDate = Carbon::parse('2024-01-15');
    $otherDate = Carbon::parse('2024-01-16');
    
    $user1 = TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    $user1->created_at = $targetDate;
    $user1->updated_at = $targetDate;
    $user1->save();
    
    $user2 = TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);
    $user2->created_at = $otherDate;
    $user2->updated_at = $otherDate;
    $user2->save();

    $filter = DateFilter::make('created_at')
        ->column('created_at');

    // Pass Carbon instance directly (not as string)
    $query = TestModel::query();
    $filter->applyToQuery($query, $targetDate);

    $results = $query->get();
    expect($query->count())->toBe(1)
        ->and($results->first()->id)->toBe($user1->id);
});

test('it applies date filter with Carbon instance in array', function () {
    $targetDate = Carbon::parse('2024-01-15');
    $otherDate = Carbon::parse('2024-01-16');
    
    $user1 = TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    $user1->created_at = $targetDate;
    $user1->updated_at = $targetDate;
    $user1->save();
    
    $user2 = TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);
    $user2->created_at = $otherDate;
    $user2->updated_at = $otherDate;
    $user2->save();

    $filter = DateFilter::make('created_at')
        ->column('created_at');

    // Pass Carbon instance in array
    $query = TestModel::query();
    $filter->applyToQuery($query, [$targetDate]);

    $results = $query->get();
    expect($query->count())->toBe(1)
        ->and($results->first()->id)->toBe($user1->id);
});

test('it applies date filter with array value', function () {
    $targetDate = '2024-01-15';
    $otherDate = '2024-01-16';
    
    $user1 = TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    $user1->created_at = $targetDate;
    $user1->updated_at = $targetDate;
    $user1->save();
    
    $user2 = TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);
    $user2->created_at = $otherDate;
    $user2->updated_at = $otherDate;
    $user2->save();

    $filter = DateFilter::make('created_at')
        ->column('created_at');

    $query = TestModel::query();
    $filter->applyToQuery($query, [$targetDate]);

    $results = $query->get();
    expect($query->count())->toBe(1)
        ->and($results->first()->id)->toBe($user1->id);
});

test('it applies custom query callback', function () {
    $date = now();
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com', 'created_at' => $date]);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com', 'created_at' => $date->copy()->addDay()]);

    $filter = DateFilter::make('created_at')
        ->query(function ($query, $date) {
            $query->whereDate('created_at', '>=', $date->format('Y-m-d'));
        });

    $query = TestModel::query();
    $filter->applyToQuery($query, $date);

    expect($query->count())->toBe(2);
});

test('it can convert to array', function () {
    $filter = DateFilter::make('created_at')
        ->label('Created At')
        ->column('created_at')
        ->min('2024-01-01')
        ->withToday();

    $array = $filter->toArray();

    expect($array['name'])->toBe('created_at')
        ->and($array['type'])->toBe('date')
        ->and($array['mode'])->toBe('single')
        ->and($array['column'])->toBe('created_at')
        ->and($array['min'])->toBe('2024-01-01')
        ->and($array['withToday'])->toBeTrue();
});

test('it supports fluent api', function () {
    $filter = DateFilter::make('created_at')
        ->label('Created At')
        ->column('created_at')
        ->min('2024-01-01')
        ->max('2024-12-31')
        ->withToday()
        ->clearable()
        ->locale('fr');

    expect($filter)->toBeInstanceOf(DateFilter::class)
        ->and($filter->getLabel())->toBe('Created At')
        ->and($filter->getColumn())->toBe('created_at')
        ->and($filter->getMin())->toBe('2024-01-01')
        ->and($filter->getMax())->toBe('2024-12-31')
        ->and($filter->getWithToday())->toBeTrue()
        ->and($filter->getClearable())->toBeTrue()
        ->and($filter->getLocale())->toBe('fr');
});

test('it does not apply filter when date is null', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $filter = DateFilter::make('created_at')
        ->column('created_at');

    $query = TestModel::query();
    $filter->applyToQuery($query, [null]);

    expect($query->count())->toBe(1);
});

test('it handles null date in array', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $filter = DateFilter::make('created_at')
        ->column('created_at');

    $query = TestModel::query();
    $filter->applyToQuery($query, ['']);

    expect($query->count())->toBe(1);
});

test('it handles empty string date', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $filter = DateFilter::make('created_at')
        ->column('created_at');

    $query = TestModel::query();
    $filter->applyToQuery($query, '');

    expect($query->count())->toBe(1);
});
