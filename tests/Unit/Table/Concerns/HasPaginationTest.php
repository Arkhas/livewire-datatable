<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Table\Concerns;

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

class HasPaginationTest extends TestCase
{
    protected function createTable(): EloquentTable
    {
        return new EloquentTable(TestModel::query());
    }

    /** @test */
    public function it_has_default_per_page_value(): void
    {
        $table = $this->createTable();

        $this->assertEquals(10, $table->getPerPage());
    }

    /** @test */
    public function it_can_set_per_page(): void
    {
        $table = $this->createTable()
            ->perPage(25);

        $this->assertEquals(25, $table->getPerPage());
    }

    /** @test */
    public function it_has_default_per_page_options(): void
    {
        $table = $this->createTable();

        $this->assertEquals([10, 25, 50, 100], $table->getPerPageOptions());
    }

    /** @test */
    public function it_can_set_per_page_options(): void
    {
        $table = $this->createTable()
            ->perPageOptions([5, 10, 20, 50]);

        $this->assertEquals([5, 10, 20, 50], $table->getPerPageOptions());
    }

    /** @test */
    public function it_supports_fluent_pagination_configuration(): void
    {
        $table = $this->createTable()
            ->perPage(20)
            ->perPageOptions([20, 40, 60, 100]);

        $this->assertEquals(20, $table->getPerPage());
        $this->assertEquals([20, 40, 60, 100], $table->getPerPageOptions());
    }
}
