<?php

use Arkhas\LivewireDatatable\Export\DatatableExport;
use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Columns\Column;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

beforeEach(function () {
    if (!interface_exists(\Maatwebsite\Excel\Concerns\FromGenerator::class)) {
        $this->markTestSkipped('DatatableExport requires maatwebsite/excel package');
    }
});

test('it can be created', function () {
    $table = (new EloquentTable(TestModel::query()))
        ->columns([Column::make('name')]);

    $export = new DatatableExport($table);

    expect($export)->toBeInstanceOf(DatatableExport::class);
});

test('it can generate data', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

    $table = (new EloquentTable(TestModel::query()))
        ->columns([
            Column::make('name')->label('Name'),
            Column::make('email')->label('Email'),
        ]);

    $export = new DatatableExport($table);

    $generator = $export->generator();
    $rows = iterator_to_array($generator);

    expect($rows)->toHaveCount(2)
        ->and($rows[0])->toHaveCount(2);
});

test('it returns headings', function () {
    $table = (new EloquentTable(TestModel::query()))
        ->columns([
            Column::make('name')->label('Name'),
            Column::make('email')->label('Email'),
        ]);

    $export = new DatatableExport($table);

    $headings = $export->headings();

    expect($headings)->toBe(['Name', 'Email']);
});

test('it returns chunk size', function () {
    $table = (new EloquentTable(TestModel::query()))
        ->columns([Column::make('name')]);

    $export = new DatatableExport($table);

    expect($export->chunkSize())->toBe(1000);
});

test('it can generate data with filters and search', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com', 'status' => 'inactive']);

    $table = (new EloquentTable(TestModel::query()))
        ->columns([
            Column::make('name')->label('Name'),
            Column::make('email')->label('Email'),
        ])
        ->filters([
            \Arkhas\LivewireDatatable\Filters\Filter::make('status')
                ->options([
                    \Arkhas\LivewireDatatable\Filters\FilterOption::make('active')
                        ->query(fn($q) => $q->where('status', 'active')),
                ]),
        ])
        ->searchable(['name']);

    $export = new DatatableExport($table, ['status' => ['active']], 'User');

    $generator = $export->generator();
    $rows = iterator_to_array($generator);

    expect($rows)->toHaveCount(1)
        ->and($rows[0])->toHaveCount(2);
});

test('it excludes checkbox and action columns from headings', function () {
    $table = (new EloquentTable(TestModel::query()))
        ->columns([
            \Arkhas\LivewireDatatable\Columns\CheckboxColumn::make(),
            Column::make('name')->label('Name'),
            \Arkhas\LivewireDatatable\Columns\ActionColumn::make(),
            Column::make('email')->label('Email'),
        ]);

    $export = new DatatableExport($table);

    $headings = $export->headings();

    expect($headings)->toBe(['Name', 'Email']);
});

test('it excludes checkbox and action columns from row data', function () {
    $model = TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $table = (new EloquentTable(TestModel::query()))
        ->columns([
            \Arkhas\LivewireDatatable\Columns\CheckboxColumn::make(),
            Column::make('name')->label('Name'),
            \Arkhas\LivewireDatatable\Columns\ActionColumn::make(),
            Column::make('email')->label('Email'),
        ]);

    $export = new DatatableExport($table);

    // Use reflection to test protected method
    $reflection = new \ReflectionClass($export);
    $method = $reflection->getMethod('getExportRow');
    $method->setAccessible(true);

    $row = $method->invoke($export, $model);

    expect($row)->toBe(['User 1', 'user1@example.com']);
});
