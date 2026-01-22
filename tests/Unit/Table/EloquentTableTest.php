<?php

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Columns\Column;
use Arkhas\LivewireDatatable\Columns\ActionColumn;
use Arkhas\LivewireDatatable\Columns\CheckboxColumn;
use Arkhas\LivewireDatatable\Actions\TableAction;
use Arkhas\LivewireDatatable\Filters\Filter;
use Arkhas\LivewireDatatable\Filters\FilterOption;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestRelatedModel;

function createEloquentTableTestTable(): EloquentTable
{
    return new EloquentTable(TestModel::query());
}

test('it can be created', function () {
    $table = createEloquentTableTestTable();

    expect($table)->toBeInstanceOf(EloquentTable::class);
});

test('it can get query', function () {
    $table = createEloquentTableTestTable();

    expect($table->getQuery())->toBeInstanceOf(\Illuminate\Database\Eloquent\Builder::class);
});

test('it clones query', function () {
    $table = createEloquentTableTestTable();

    $query1 = $table->getQuery();
    $query2 = $table->getQuery();

    expect($query1)->not->toBe($query2);
});

test('it can get model class', function () {
    $table = createEloquentTableTestTable();

    expect($table->getModel())->toBe(TestModel::class);
});

test('it can set and get export name', function () {
    $table = createEloquentTableTestTable()
        ->exportName('users_export');

    expect($table->getExportName())->toBe('users_export');
});

test('it has default export name', function () {
    $table = createEloquentTableTestTable();

    expect($table->getExportName())->toBe('export');
});

test('it can build query without modifications', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

    $table = createEloquentTableTestTable()
        ->columns([
            Column::make('name'),
            Column::make('email'),
        ]);

    $query = $table->buildQuery();

    expect($query->count())->toBe(2);
});

test('it can build query with search', function () {
    TestModel::create(['name' => 'John Doe', 'email' => 'john@example.com']);
    TestModel::create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

    $table = createEloquentTableTestTable()
        ->columns([
            Column::make('name'),
            Column::make('email'),
        ])
        ->searchable(['name']);

    $query = $table->buildQuery([], 'John');

    expect($query->count())->toBe(1)
        ->and($query->first()->name)->toBe('John Doe');
});

test('it can search from columns', function () {
    TestModel::create(['name' => 'Test User', 'email' => 'test@example.com']);
    TestModel::create(['name' => 'Another User', 'email' => 'another@example.com']);

    $table = createEloquentTableTestTable()
        ->columns([
            Column::make('name'),
            Column::make('email'),
        ])
        ->searchable();

    $query = $table->buildQuery([], 'test');

    expect($query->count())->toBe(1);
});

test('it excludes special columns from search', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $table = createEloquentTableTestTable()
        ->columns([
            CheckboxColumn::make(),
            Column::make('name'),
            ActionColumn::make(),
        ])
        ->searchable();

    // Should not throw error and should work properly
    $query = $table->buildQuery([], 'User');

    expect($query->count())->toBe(1);
});

test('it can search relations', function () {
    $model = TestModel::create(['name' => 'Parent', 'email' => 'parent@example.com']);
    TestRelatedModel::create(['test_model_id' => $model->id, 'title' => 'Child Title']);

    $table = new EloquentTable(TestModel::query()->with('relatedModels'))
        ->columns([
            Column::make('name'),
        ])
        ->searchable(['relatedModels.title']);

    $query = $table->buildQuery([], 'Child');

    expect($query->count())->toBe(1);
});

test('it does not search when disabled', function () {
    TestModel::create(['name' => 'Searchable', 'email' => 'search@example.com']);

    $table = createEloquentTableTestTable()
        ->columns([Column::make('name')])
        ->notSearchable();

    $query = $table->buildQuery([], 'xyz');

    // Should return all results since search is disabled
    expect($query->count())->toBe(1);
});

test('it can build query with filters', function () {
    TestModel::create(['name' => 'Active User', 'email' => 'active@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'Inactive User', 'email' => 'inactive@example.com', 'status' => 'inactive']);

    $table = createEloquentTableTestTable()
        ->columns([Column::make('name')])
        ->filters([
            Filter::make('status')
                ->options([
                    FilterOption::make('active')
                        ->query(fn($q) => $q->where('status', 'active')),
                ]),
        ]);

    $query = $table->buildQuery(['status' => ['active']]);

    expect($query->count())->toBe(1)
        ->and($query->first()->name)->toBe('Active User');
});

test('it can apply column filters', function () {
    TestModel::create(['name' => 'User A', 'email' => 'a@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'User B', 'email' => 'b@example.com', 'status' => 'inactive']);

    $table = createEloquentTableTestTable()
        ->columns([
            Column::make('name'),
            Column::make('status')
                ->filter(fn($query, $value) => $query->where('status', $value)),
        ]);

    $query = $table->buildQuery(['status' => 'active']);

    expect($query->count())->toBe(1);
});

test('it can build query with sorting', function () {
    TestModel::create(['name' => 'Zebra', 'email' => 'z@example.com']);
    TestModel::create(['name' => 'Apple', 'email' => 'a@example.com']);
    TestModel::create(['name' => 'Mango', 'email' => 'm@example.com']);

    $table = createEloquentTableTestTable()
        ->columns([
            Column::make('name')->sortable(),
        ]);

    $query = $table->buildQuery([], null, 'name', 'asc');
    $results = $query->get();

    expect($results->first()->name)->toBe('Apple')
        ->and($results->last()->name)->toBe('Zebra');
});

test('it can sort descending', function () {
    TestModel::create(['name' => 'Zebra', 'email' => 'z@example.com']);
    TestModel::create(['name' => 'Apple', 'email' => 'a@example.com']);

    $table = createEloquentTableTestTable()
        ->columns([
            Column::make('name')->sortable(),
        ]);

    $query = $table->buildQuery([], null, 'name', 'desc');
    $results = $query->get();

    expect($results->first()->name)->toBe('Zebra');
});

test('it uses custom sort column', function () {
    TestModel::create(['name' => 'Test', 'email' => 'z@example.com']);
    TestModel::create(['name' => 'Test', 'email' => 'a@example.com']);

    $table = createEloquentTableTestTable()
        ->columns([
            Column::make('name')
                ->sortable()
                ->sortBy('email'),
        ]);

    $query = $table->buildQuery([], null, 'name', 'asc');
    $results = $query->get();

    expect($results->first()->email)->toBe('a@example.com');
});

test('it does not sort non sortable columns', function () {
    $user1 = TestModel::create(['name' => 'Zebra', 'email' => 'z@example.com']);
    $user2 = TestModel::create(['name' => 'Apple', 'email' => 'a@example.com']);

    $table = createEloquentTableTestTable()
        ->columns([
            Column::make('name')->sortable(false),
        ]);

    $query = $table->buildQuery([], null, 'name', 'asc');
    $results = $query->get();

    // Should be in insertion order since sorting is disabled
    expect($results->first()->id)->toBe($user1->id);
});

test('it ignores sorting for unknown column', function () {
    TestModel::create(['name' => 'User', 'email' => 'user@example.com']);

    $table = createEloquentTableTestTable()
        ->columns([
            Column::make('name'),
        ]);

    // Should not throw error
    $query = $table->buildQuery([], null, 'unknown_column', 'asc');

    expect($query->count())->toBe(1);
});

test('it can paginate results', function () {
    for ($i = 1; $i <= 25; $i++) {
        TestModel::create(['name' => "User $i", 'email' => "user$i@example.com"]);
    }

    $table = createEloquentTableTestTable()
        ->columns([Column::make('name')]);

    $paginated = $table->paginate([], null, null, 'asc', 10);

    expect($paginated->count())->toBe(10)
        ->and($paginated->total())->toBe(25)
        ->and($paginated->lastPage())->toBe(3);
});

test('it can convert to array', function () {
    $table = createEloquentTableTestTable()
        ->columns([
            Column::make('name'),
            Column::make('email'),
        ])
        ->filters([
            Filter::make('status'),
        ])
        ->actions([
            TableAction::make('delete'),
        ])
        ->exportName('users')
        ->searchable(['name']);

    $array = $table->toArray();

    expect($array)->toHaveKey('columns')
        ->and($array)->toHaveKey('filters')
        ->and($array)->toHaveKey('actions')
        ->and($array)->toHaveKey('exportName')
        ->and($array)->toHaveKey('searchable')
        ->and($array['columns'])->toHaveCount(2)
        ->and($array['filters'])->toHaveCount(1)
        ->and($array['actions'])->toHaveCount(1)
        ->and($array['exportName'])->toBe('users')
        ->and($array['searchable'])->toBeTrue();
});

test('it combines all query modifications', function () {
    TestModel::create(['name' => 'Active John', 'email' => 'john@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'Active Jane', 'email' => 'jane@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'Inactive Bob', 'email' => 'bob@example.com', 'status' => 'inactive']);

    $table = createEloquentTableTestTable()
        ->columns([
            Column::make('name')->sortable(),
        ])
        ->filters([
            Filter::make('status')
                ->options([
                    FilterOption::make('active')
                        ->query(fn($q) => $q->where('status', 'active')),
                ]),
        ])
        ->searchable(['name']);

    $query = $table->buildQuery(
        ['status' => ['active']],
        'Jane',
        'name',
        'asc'
    );

    expect($query->count())->toBe(1)
        ->and($query->first()->name)->toBe('Active Jane');
});

test('it returns empty array when search columns not set and search from columns disabled', function () {
    $table = createEloquentTableTestTable()
        ->columns([Column::make('name')])
        ->searchable(['explicit_column']); // This sets searchFromColumns to false

    // Use reflection to access and modify protected properties
    $reflection = new \ReflectionClass($table);
    
    // Clear searchColumns to make it empty
    $searchColumnsProperty = $reflection->getProperty('searchColumns');
    $searchColumnsProperty->setAccessible(true);
    $searchColumnsProperty->setValue($table, []);
    
    // Set searchFromColumns to false
    $searchFromColumnsProperty = $reflection->getProperty('searchFromColumns');
    $searchFromColumnsProperty->setAccessible(true);
    $searchFromColumnsProperty->setValue($table, false);

    // Now call getResolvedSearchColumns - should return []
    $method = $reflection->getMethod('getResolvedSearchColumns');
    $method->setAccessible(true);

    $columns = $method->invoke($table);

    // When searchColumns is empty and shouldSearchFromColumns is false, should return []
    expect($columns)->toBe([]);
});
