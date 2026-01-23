<?php

use Arkhas\LivewireDatatable\Filters\RangeFilter;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;
use Illuminate\Support\Carbon;

beforeEach(function () {
    TestModel::query()->delete();
});

test('it can be created with make', function () {
    $filter = RangeFilter::make('date_range');

    expect($filter)
        ->toBeInstanceOf(RangeFilter::class)
        ->and($filter->getName())->toBe('date_range');
    
    $array = $filter->toArray();
    expect($array['type'])->toBe('range');
});

test('it returns range mode', function () {
    $filter = RangeFilter::make('date_range');

    expect($filter->getMode())->toBe('range');
});

test('it can set column', function () {
    $filter = RangeFilter::make('date_range')
        ->column('created_at');

    expect($filter->getColumn())->toBe('created_at');
});

test('it defaults column to filter name', function () {
    $filter = RangeFilter::make('date_range');

    expect($filter->getColumn())->toBe('date_range');
});

test('it can enable presets', function () {
    $filter = RangeFilter::make('date_range')
        ->withPresets();

    expect($filter->getWithPresets())->toBeTrue();
});

test('it can disable presets', function () {
    $filter = RangeFilter::make('date_range')
        ->withPresets()
        ->withPresets(false);

    expect($filter->getWithPresets())->toBeFalse();
});

test('it can set presets', function () {
    $filter = RangeFilter::make('date_range')
        ->presets('today yesterday thisWeek');

    expect($filter->getPresets())->toBe('today yesterday thisWeek');
});

test('it can set min range', function () {
    $filter = RangeFilter::make('date_range')
        ->minRange(3);

    expect($filter->getMinRange())->toBe(3);
});

test('it can set max range', function () {
    $filter = RangeFilter::make('date_range')
        ->maxRange(30);

    expect($filter->getMaxRange())->toBe(30);
});

test('it can set date picker properties', function () {
    $filter = RangeFilter::make('date_range')
        ->min('2024-01-01')
        ->max('2024-12-31')
        ->withToday()
        ->clearable();

    expect($filter->getMin())->toBe('2024-01-01')
        ->and($filter->getMax())->toBe('2024-12-31')
        ->and($filter->getWithToday())->toBeTrue()
        ->and($filter->getClearable())->toBeTrue();
});

test('it does not apply filter with empty values', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com', 'created_at' => now()]);

    $filter = RangeFilter::make('date_range')
        ->column('created_at');

    $query = TestModel::query();
    $filter->applyToQuery($query, []);

    expect($query->count())->toBe(1);
});

test('it applies range filter with string format', function () {
    $start = '2024-01-15';
    $end = '2024-01-17';
    $range = "{$start}/{$end}";

    $user1 = TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    $user1->created_at = $start;
    $user1->updated_at = $start;
    $user1->save();
    
    $user2 = TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);
    $user2->created_at = '2024-01-18';
    $user2->updated_at = '2024-01-18';
    $user2->save();

    $filter = RangeFilter::make('date_range')
        ->column('created_at');

    $query = TestModel::query();
    $filter->applyToQuery($query, [$range]);

    $results = $query->get();
    expect($query->count())->toBe(1)
        ->and($results->first()->id)->toBe($user1->id);
});

test('it applies range filter with array format', function () {
    $start = '2024-01-15';
    $end = '2024-01-17';

    $user1 = TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    $user1->created_at = $start;
    $user1->updated_at = $start;
    $user1->save();
    
    $user2 = TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);
    $user2->created_at = '2024-01-18';
    $user2->updated_at = '2024-01-18';
    $user2->save();

    $filter = RangeFilter::make('date_range')
        ->column('created_at');

    $query = TestModel::query();
    $filter->applyToQuery($query, ['start' => $start, 'end' => $end]);

    $results = $query->get();
    expect($query->count())->toBe(1)
        ->and($results->first()->id)->toBe($user1->id);
});

test('it applies range filter with nested array format', function () {
    $start = '2024-01-15';
    $end = '2024-01-17';

    $user1 = TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    $user1->created_at = $start;
    $user1->updated_at = $start;
    $user1->save();
    
    $user2 = TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);
    $user2->created_at = '2024-01-18';
    $user2->updated_at = '2024-01-18';
    $user2->save();

    $filter = RangeFilter::make('date_range')
        ->column('created_at');

    $query = TestModel::query();
    $filter->applyToQuery($query, [['start' => $start, 'end' => $end]]);

    $results = $query->get();
    expect($query->count())->toBe(1)
        ->and($results->first()->id)->toBe($user1->id);
});

test('it applies custom query callback', function () {
    $start = '2024-01-15';
    $end = '2024-01-17';

    $user1 = TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    $user1->created_at = $start;
    $user1->updated_at = $start;
    $user1->save();
    
    $user2 = TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);
    $user2->created_at = '2024-01-18';
    $user2->updated_at = '2024-01-18';
    $user2->save();

    $filter = RangeFilter::make('date_range')
        ->column('created_at')
        ->query(function ($query, $dates) {
            [$startDate, $endDate] = $dates;
            $query->whereDate('created_at', '>=', $startDate->format('Y-m-d'))
                ->whereDate('created_at', '<=', $endDate->format('Y-m-d'));
        });

    $query = TestModel::query();
    $filter->applyToQuery($query, [
        'start' => $start,
        'end' => $end,
    ]);

    $results = $query->get();
    expect($query->count())->toBe(1)
        ->and($results->first()->id)->toBe($user1->id);
});

test('it can convert to array', function () {
    $filter = RangeFilter::make('date_range')
        ->label('Date Range')
        ->column('created_at')
        ->withPresets()
        ->presets('today yesterday thisWeek')
        ->minRange(3)
        ->maxRange(30)
        ->min('2024-01-01');

    $array = $filter->toArray();

    expect($array['name'])->toBe('date_range')
        ->and($array['type'])->toBe('range')
        ->and($array['mode'])->toBe('range')
        ->and($array['column'])->toBe('created_at')
        ->and($array['withPresets'])->toBeTrue()
        ->and($array['presets'])->toBe('today yesterday thisWeek')
        ->and($array['minRange'])->toBe(3)
        ->and($array['maxRange'])->toBe(30)
        ->and($array['min'])->toBe('2024-01-01');
});

test('it supports fluent api', function () {
    $filter = RangeFilter::make('date_range')
        ->label('Date Range')
        ->column('created_at')
        ->withPresets()
        ->presets('today yesterday thisWeek last7Days')
        ->minRange(3)
        ->maxRange(30)
        ->min('2024-01-01')
        ->max('2024-12-31')
        ->withToday()
        ->clearable();

    expect($filter)->toBeInstanceOf(RangeFilter::class)
        ->and($filter->getLabel())->toBe('Date Range')
        ->and($filter->getColumn())->toBe('created_at')
        ->and($filter->getWithPresets())->toBeTrue()
        ->and($filter->getPresets())->toBe('today yesterday thisWeek last7Days')
        ->and($filter->getMinRange())->toBe(3)
        ->and($filter->getMaxRange())->toBe(30)
        ->and($filter->getMin())->toBe('2024-01-01')
        ->and($filter->getMax())->toBe('2024-12-31')
        ->and($filter->getWithToday())->toBeTrue()
        ->and($filter->getClearable())->toBeTrue();
});

test('it handles string values directly', function () {
    $start = '2024-01-15';
    $end = '2024-01-17';
    $range = "{$start}/{$end}";

    $user1 = TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    $user1->created_at = $start;
    $user1->updated_at = $start;
    $user1->save();

    $filter = RangeFilter::make('date_range')
        ->column('created_at');

    $query = TestModel::query();
    $filter->applyToQuery($query, $range);

    $results = $query->get();
    expect($query->count())->toBe(1)
        ->and($results->first()->id)->toBe($user1->id);
});

test('it handles DateRange object format', function () {
    $start = '2024-01-15';
    $end = '2024-01-17';

    $user1 = TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    $user1->created_at = $start;
    $user1->updated_at = $start;
    $user1->save();

    // Create a mock DateRange object
    $dateRange = new class($start, $end) {
        private $start;
        private $end;

        public function __construct($start, $end) {
            $this->start = $start;
            $this->end = $end;
        }

        public function start() {
            return $this->start;
        }

        public function end() {
            return $this->end;
        }
    };

    $filter = RangeFilter::make('date_range')
        ->column('created_at');

    $query = TestModel::query();
    $filter->applyToQuery($query, [$dateRange]);

    $results = $query->get();
    expect($query->count())->toBe(1)
        ->and($results->first()->id)->toBe($user1->id);
});
