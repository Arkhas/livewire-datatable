<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Table\Concerns;

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Filters\Filter;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

class HasFiltersTest extends TestCase
{
    protected function createTable(): EloquentTable
    {
        return new EloquentTable(TestModel::query());
    }

    /** @test */
    public function it_can_set_filters(): void
    {
        $filters = [
            Filter::make('status'),
            Filter::make('category'),
        ];

        $table = $this->createTable()
            ->filters($filters);

        $this->assertSame($filters, $table->getFilters());
    }

    /** @test */
    public function it_returns_empty_filters_by_default(): void
    {
        $table = $this->createTable();

        $this->assertEquals([], $table->getFilters());
    }

    /** @test */
    public function it_can_get_filter_by_name(): void
    {
        $statusFilter = Filter::make('status');
        $categoryFilter = Filter::make('category');

        $table = $this->createTable()
            ->filters([$statusFilter, $categoryFilter]);

        $this->assertSame($statusFilter, $table->getFilter('status'));
        $this->assertSame($categoryFilter, $table->getFilter('category'));
    }

    /** @test */
    public function it_returns_null_for_missing_filter(): void
    {
        $table = $this->createTable()
            ->filters([Filter::make('status')]);

        $this->assertNull($table->getFilter('category'));
    }

    /** @test */
    public function it_can_count_active_filters(): void
    {
        $table = $this->createTable()
            ->filters([
                Filter::make('status'),
                Filter::make('category'),
            ]);

        $this->assertEquals(0, $table->getActiveFiltersCount([]));
        $this->assertEquals(1, $table->getActiveFiltersCount(['status' => ['active']]));
        $this->assertEquals(2, $table->getActiveFiltersCount([
            'status' => ['active'],
            'category' => ['tech', 'sport'],
        ]));
    }

    /** @test */
    public function it_ignores_empty_filter_values_in_count(): void
    {
        $table = $this->createTable()
            ->filters([
                Filter::make('status'),
                Filter::make('category'),
            ]);

        $count = $table->getActiveFiltersCount([
            'status' => ['active'],
            'category' => [],
        ]);

        $this->assertEquals(1, $count);
    }
}
