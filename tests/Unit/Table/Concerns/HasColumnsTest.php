<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Table\Concerns;

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Columns\Column;
use Arkhas\LivewireDatatable\Columns\ActionColumn;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

class HasColumnsTest extends TestCase
{
    protected function createTable(): EloquentTable
    {
        return new EloquentTable(TestModel::query());
    }

    /** @test */
    public function it_can_set_columns(): void
    {
        $columns = [
            Column::make('name'),
            Column::make('email'),
        ];

        $table = $this->createTable()
            ->columns($columns);

        $this->assertSame($columns, $table->getColumns());
    }

    /** @test */
    public function it_returns_empty_columns_by_default(): void
    {
        $table = $this->createTable();

        $this->assertEquals([], $table->getColumns());
    }

    /** @test */
    public function it_can_get_visible_columns(): void
    {
        $table = $this->createTable()
            ->columns([
                Column::make('name'),
                Column::make('email')->hidden(),
                Column::make('status'),
            ]);

        $visible = $table->getVisibleColumns();

        $this->assertCount(2, $visible);
    }

    /** @test */
    public function it_can_get_column_by_name(): void
    {
        $nameColumn = Column::make('name');
        $emailColumn = Column::make('email');

        $table = $this->createTable()
            ->columns([$nameColumn, $emailColumn]);

        $this->assertSame($nameColumn, $table->getColumn('name'));
        $this->assertSame($emailColumn, $table->getColumn('email'));
    }

    /** @test */
    public function it_returns_null_for_missing_column(): void
    {
        $table = $this->createTable()
            ->columns([Column::make('name')]);

        $this->assertNull($table->getColumn('email'));
    }

    /** @test */
    public function it_can_get_sortable_columns(): void
    {
        $table = $this->createTable()
            ->columns([
                Column::make('name')->sortable(),
                Column::make('email')->sortable(false),
                Column::make('status')->sortable(),
            ]);

        $sortable = $table->getSortableColumns();

        $this->assertCount(2, $sortable);
    }

    /** @test */
    public function it_can_get_toggable_columns(): void
    {
        $table = $this->createTable()
            ->columns([
                Column::make('name')->toggable(),
                Column::make('email')->toggable(false),
                ActionColumn::make(), // Not toggable by default
            ]);

        $toggable = $table->getToggableColumns();

        $this->assertCount(1, $toggable);
    }

    /** @test */
    public function it_supports_fluent_columns(): void
    {
        $table = $this->createTable()
            ->columns([
                Column::make('name')
                    ->label('Full Name')
                    ->sortable()
                    ->toggable(),
            ]);

        $column = $table->getColumn('name');

        $this->assertEquals('Full Name', $column->getLabel());
        $this->assertTrue($column->isSortable());
        $this->assertTrue($column->isToggable());
    }
}
