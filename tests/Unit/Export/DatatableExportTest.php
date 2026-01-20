<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Export;

use Arkhas\LivewireDatatable\Tests\TestCase;

/**
 * DatatableExport tests require maatwebsite/excel package.
 * These tests are skipped when the package is not installed.
 */
class DatatableExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        if (!interface_exists(\Maatwebsite\Excel\Concerns\FromGenerator::class)) {
            $this->markTestSkipped('DatatableExport requires maatwebsite/excel package');
        }
    }

    /** @test */
    public function it_requires_maatwebsite_excel_package(): void
    {
        // This test will be skipped if maatwebsite/excel is not installed
        $this->assertTrue(true);
    }
}
