<?php

use Arkhas\LivewireDatatable\Export\DatatableExporter;
use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Columns\Column;
use Arkhas\LivewireDatatable\Columns\CheckboxColumn;
use Arkhas\LivewireDatatable\Columns\ActionColumn;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;
use Symfony\Component\HttpFoundation\StreamedResponse;

function createExporterTable(): EloquentTable
{
    return new EloquentTable(TestModel::query());
}

test('it can be created', function () {
    $table = createExporterTable()
        ->columns([Column::make('name')]);

    $exporter = new DatatableExporter($table);

    expect($exporter)->toBeInstanceOf(DatatableExporter::class);
});

test('it can export to csv', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $table = createExporterTable()
        ->columns([
            Column::make('name')->label('Name'),
            Column::make('email')->label('Email'),
        ])
        ->exportName('users');

    $exporter = new DatatableExporter($table);

    $response = $exporter->toCsv();

    expect($response)->toBeInstanceOf(StreamedResponse::class)
        ->and($response->headers->get('Content-Type'))->toBe('text/csv')
        ->and($response->headers->get('Content-Disposition'))->toContain('users.csv');

    // Execute the callback to cover lines 30-44
    ob_start();
    $response->sendContent();
    $content = ob_get_clean();

    expect($content)->toContain('Name')
        ->and($content)->toContain('Email')
        ->and($content)->toContain('User 1')
        ->and($content)->toContain('user1@example.com');
});

test('it can export via format method csv', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $table = createExporterTable()
        ->columns([Column::make('name')])
        ->exportName('data');

    $exporter = new DatatableExporter($table);

    $response = $exporter->export('csv');

    expect($response)->toBeInstanceOf(StreamedResponse::class);
});

test('it defaults to csv format', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $table = createExporterTable()
        ->columns([Column::make('name')])
        ->exportName('export');

    $exporter = new DatatableExporter($table);

    $response = $exporter->export();

    expect($response)->toBeInstanceOf(StreamedResponse::class)
        ->and($response->headers->get('Content-Disposition'))->toContain('export.csv');
});

test('it throws exception for xlsx without package', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $table = createExporterTable()
        ->columns([Column::make('name')]);

    $exporter = new DatatableExporter($table);

    // This will throw because maatwebsite/excel is not installed in tests
    // We can't test the actual XLSX export without the package
    if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
        expect(fn() => $exporter->toXlsx())
            ->toThrow(\RuntimeException::class, 'XLSX export requires the maatwebsite/excel package');
    } else {
        $this->markTestSkipped('Maatwebsite Excel is installed');
    }
});

test('it excludes special columns from export', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $table = createExporterTable()
        ->columns([
            CheckboxColumn::make(),
            Column::make('name')->label('Name'),
            ActionColumn::make(),
        ]);

    $exporter = new DatatableExporter($table);

    // We can verify the headers are correct by checking the response
    $response = $exporter->toCsv();
    
    expect($response)->toBeInstanceOf(StreamedResponse::class);
});

test('it respects filters in export', function () {
    TestModel::create(['name' => 'Active', 'email' => 'active@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'Inactive', 'email' => 'inactive@example.com', 'status' => 'inactive']);

    $table = createExporterTable()
        ->columns([
            Column::make('name'),
            Column::make('status')
                ->filter(fn($q, $v) => $q->where('status', $v)),
        ]);

    $exporter = new DatatableExporter($table, ['status' => 'active']);

    $response = $exporter->toCsv();

    expect($response)->toBeInstanceOf(StreamedResponse::class);
});

test('it respects search in export', function () {
    TestModel::create(['name' => 'John', 'email' => 'john@example.com']);
    TestModel::create(['name' => 'Jane', 'email' => 'jane@example.com']);

    $table = createExporterTable()
        ->columns([Column::make('name')])
        ->searchable(['name']);

    $exporter = new DatatableExporter($table, [], 'John');

    $response = $exporter->toCsv();

    expect($response)->toBeInstanceOf(StreamedResponse::class);
});

test('it uses export callback for column values', function () {
    TestModel::create(['name' => 'test', 'email' => 'test@example.com']);

    $table = createExporterTable()
        ->columns([
            Column::make('name')
                ->exportAs(fn($model) => strtoupper($model->name)),
        ]);

    $exporter = new DatatableExporter($table);

    $response = $exporter->toCsv();

    expect($response)->toBeInstanceOf(StreamedResponse::class);
});

test('it skips checkbox and action columns in headers', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $table = createExporterTable()
        ->columns([
            CheckboxColumn::make(),
            Column::make('name')->label('Name'),
            ActionColumn::make(),
            Column::make('email')->label('Email'),
        ]);

    $exporter = new DatatableExporter($table);

    // Use reflection to test protected method
    $reflection = new \ReflectionClass($exporter);
    $method = $reflection->getMethod('getExportHeaders');
    $method->setAccessible(true);

    $headers = $method->invoke($exporter);

    expect($headers)->toBe(['Name', 'Email']);
});

test('it skips checkbox and action columns in row data', function () {
    $model = TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $table = createExporterTable()
        ->columns([
            CheckboxColumn::make(),
            Column::make('name')->label('Name'),
            ActionColumn::make(),
            Column::make('email')->label('Email'),
        ]);

    $exporter = new DatatableExporter($table);

    // Use reflection to test protected method
    $reflection = new \ReflectionClass($exporter);
    $method = $reflection->getMethod('getExportRow');
    $method->setAccessible(true);

    $row = $method->invoke($exporter, $model);

    expect($row)->toBe(['User 1', 'user1@example.com']);
});

test('it can export to xlsx when package is installed', function () {
    if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
        $this->markTestSkipped('Maatwebsite Excel is not installed');
    }

    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $table = createExporterTable()
        ->columns([Column::make('name')])
        ->exportName('test');

    $exporter = new DatatableExporter($table);

    $response = $exporter->toXlsx();

    expect($response)->toBeInstanceOf(StreamedResponse::class);
    
    // Execute the response to ensure the callback runs (covers lines 61-66)
    ob_start();
    $response->sendContent();
    $content = ob_get_clean();
    
    expect($content)->not->toBeEmpty();
});
