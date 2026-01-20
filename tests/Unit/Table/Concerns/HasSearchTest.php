<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Table\Concerns;

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

class HasSearchTest extends TestCase
{
    protected function createTable(): EloquentTable
    {
        return new EloquentTable(TestModel::query());
    }

    /** @test */
    public function it_is_searchable_by_default(): void
    {
        $table = $this->createTable();

        $this->assertTrue($table->isSearchable());
    }

    /** @test */
    public function it_can_enable_searchable_with_columns(): void
    {
        $table = $this->createTable()
            ->searchable(['name', 'email']);

        $this->assertTrue($table->isSearchable());
        $this->assertEquals(['name', 'email'], $table->getSearchColumns());
    }

    /** @test */
    public function it_can_enable_searchable_without_columns(): void
    {
        $table = $this->createTable()
            ->searchable();

        $this->assertTrue($table->isSearchable());
        $this->assertEquals([], $table->getSearchColumns());
    }

    /** @test */
    public function it_can_disable_search(): void
    {
        $table = $this->createTable()
            ->notSearchable();

        $this->assertFalse($table->isSearchable());
    }

    /** @test */
    public function it_should_search_from_columns_by_default(): void
    {
        $table = $this->createTable();

        $this->assertTrue($table->shouldSearchFromColumns());
    }

    /** @test */
    public function it_should_not_search_from_columns_when_explicit_columns_set(): void
    {
        $table = $this->createTable()
            ->searchable(['name']);

        $this->assertFalse($table->shouldSearchFromColumns());
    }

    /** @test */
    public function it_has_default_search_placeholder(): void
    {
        $table = $this->createTable();

        $this->assertEquals('Search...', $table->getSearchPlaceholder());
    }

    /** @test */
    public function it_can_set_search_placeholder(): void
    {
        $table = $this->createTable()
            ->searchPlaceholder('Search users...');

        $this->assertEquals('Search users...', $table->getSearchPlaceholder());
    }

    /** @test */
    public function it_returns_empty_search_columns_by_default(): void
    {
        $table = $this->createTable();

        $this->assertEquals([], $table->getSearchColumns());
    }

    /** @test */
    public function it_supports_fluent_search_configuration(): void
    {
        $table = $this->createTable()
            ->searchable(['name', 'email'])
            ->searchPlaceholder('Find users...');

        $this->assertTrue($table->isSearchable());
        $this->assertEquals(['name', 'email'], $table->getSearchColumns());
        $this->assertEquals('Find users...', $table->getSearchPlaceholder());
    }
}
