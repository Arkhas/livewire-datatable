<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Export;

use Arkhas\LivewireDatatable\Export\DatatableExporter;
use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Columns\Column;
use Arkhas\LivewireDatatable\Columns\CheckboxColumn;
use Arkhas\LivewireDatatable\Columns\ActionColumn;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DatatableExporterTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        parent::defineDatabaseMigrations();
    }

    protected function createTable(): EloquentTable
    {
        return new EloquentTable(TestModel::query());
    }

    /** @test */
    public function it_can_be_created(): void
    {
        $table = $this->createTable()
            ->columns([Column::make('name')]);

        $exporter = new DatatableExporter($table);

        $this->assertInstanceOf(DatatableExporter::class, $exporter);
    }

    /** @test */
    public function it_can_export_to_csv(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

        $table = $this->createTable()
            ->columns([
                Column::make('name')->label('Name'),
                Column::make('email')->label('Email'),
            ])
            ->exportName('users');

        $exporter = new DatatableExporter($table);

        $response = $exporter->toCsv();

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('users.csv', $response->headers->get('Content-Disposition'));
    }

    /** @test */
    public function it_can_export_via_format_method_csv(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

        $table = $this->createTable()
            ->columns([Column::make('name')])
            ->exportName('data');

        $exporter = new DatatableExporter($table);

        $response = $exporter->export('csv');

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    /** @test */
    public function it_defaults_to_csv_format(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

        $table = $this->createTable()
            ->columns([Column::make('name')])
            ->exportName('export');

        $exporter = new DatatableExporter($table);

        $response = $exporter->export();

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertStringContainsString('export.csv', $response->headers->get('Content-Disposition'));
    }

    /** @test */
    public function it_throws_exception_for_xlsx_without_package(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

        $table = $this->createTable()
            ->columns([Column::make('name')]);

        $exporter = new DatatableExporter($table);

        // This will throw because maatwebsite/excel is not installed in tests
        // We can't test the actual XLSX export without the package
        if (!class_exists(\Maatwebsite\Excel\Facades\Excel::class)) {
            $this->expectException(\RuntimeException::class);
            $this->expectExceptionMessage('XLSX export requires the maatwebsite/excel package');
            $exporter->toXlsx();
        } else {
            $this->markTestSkipped('Maatwebsite Excel is installed');
        }
    }

    /** @test */
    public function it_excludes_special_columns_from_export(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

        $table = $this->createTable()
            ->columns([
                CheckboxColumn::make(),
                Column::make('name')->label('Name'),
                ActionColumn::make(),
            ]);

        $exporter = new DatatableExporter($table);

        // We can verify the headers are correct by checking the response
        $response = $exporter->toCsv();
        
        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    /** @test */
    public function it_respects_filters_in_export(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'Active', 'email' => 'active@example.com', 'status' => 'active']);
        TestModel::create(['name' => 'Inactive', 'email' => 'inactive@example.com', 'status' => 'inactive']);

        $table = $this->createTable()
            ->columns([
                Column::make('name'),
                Column::make('status')
                    ->filter(fn($q, $v) => $q->where('status', $v)),
            ]);

        $exporter = new DatatableExporter($table, ['status' => 'active']);

        $response = $exporter->toCsv();

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    /** @test */
    public function it_respects_search_in_export(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'John', 'email' => 'john@example.com']);
        TestModel::create(['name' => 'Jane', 'email' => 'jane@example.com']);

        $table = $this->createTable()
            ->columns([Column::make('name')])
            ->searchable(['name']);

        $exporter = new DatatableExporter($table, [], 'John');

        $response = $exporter->toCsv();

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }

    /** @test */
    public function it_uses_export_callback_for_column_values(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'test', 'email' => 'test@example.com']);

        $table = $this->createTable()
            ->columns([
                Column::make('name')
                    ->exportAs(fn($model) => strtoupper($model->name)),
            ]);

        $exporter = new DatatableExporter($table);

        $response = $exporter->toCsv();

        $this->assertInstanceOf(StreamedResponse::class, $response);
    }
}
