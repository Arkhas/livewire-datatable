<?php

namespace Arkhas\LivewireDatatable\Tests\Unit;

use Arkhas\LivewireDatatable\LivewireDatatableServiceProvider;
use Arkhas\LivewireDatatable\Tests\TestCase;

class LivewireDatatableServiceProviderTest extends TestCase
{
    /** @test */
    public function it_registers_config(): void
    {
        $this->assertNotNull(config('livewire-datatable'));
        $this->assertIsArray(config('livewire-datatable'));
    }

    /** @test */
    public function it_has_default_per_page_config(): void
    {
        $this->assertEquals(10, config('livewire-datatable.per_page'));
    }

    /** @test */
    public function it_has_default_sort_direction_config(): void
    {
        $this->assertEquals('asc', config('livewire-datatable.default_sort_direction'));
    }

    /** @test */
    public function it_has_export_chunk_size_config(): void
    {
        $this->assertEquals(1000, config('livewire-datatable.export.chunk_size'));
    }

    /** @test */
    public function it_loads_views(): void
    {
        $this->assertTrue(
            view()->exists('livewire-datatable::datatable')
        );
    }

    /** @test */
    public function it_registers_make_datatable_command(): void
    {
        $commands = \Artisan::all();
        
        $this->assertArrayHasKey('make:datatable', $commands);
    }
}
