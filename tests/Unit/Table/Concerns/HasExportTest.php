<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Table\Concerns;

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

class HasExportTest extends TestCase
{
    protected function createTable(): EloquentTable
    {
        return new EloquentTable(TestModel::query());
    }

    /** @test */
    public function it_is_exportable_by_default(): void
    {
        $table = $this->createTable();

        $this->assertTrue($table->isExportable());
    }

    /** @test */
    public function it_can_disable_export(): void
    {
        $table = $this->createTable()
            ->exportable(false);

        $this->assertFalse($table->isExportable());
    }

    /** @test */
    public function it_can_enable_export(): void
    {
        $table = $this->createTable()
            ->exportable(false)
            ->exportable(true);

        $this->assertTrue($table->isExportable());
    }

    /** @test */
    public function it_has_default_export_formats(): void
    {
        $table = $this->createTable();

        $this->assertEquals(['csv', 'xlsx'], $table->getExportFormats());
    }

    /** @test */
    public function it_can_set_export_formats(): void
    {
        $table = $this->createTable()
            ->exportFormats(['csv', 'pdf']);

        $this->assertEquals(['csv', 'pdf'], $table->getExportFormats());
    }

    /** @test */
    public function it_supports_fluent_export_configuration(): void
    {
        $table = $this->createTable()
            ->exportable()
            ->exportFormats(['csv', 'xlsx', 'pdf']);

        $this->assertTrue($table->isExportable());
        $this->assertEquals(['csv', 'xlsx', 'pdf'], $table->getExportFormats());
    }
}
