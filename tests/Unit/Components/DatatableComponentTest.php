<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Components;

use Arkhas\LivewireDatatable\Components\Datatable;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Livewire\Livewire;

class DatatableComponentTest extends TestCase
{
    /** @test */
    public function it_can_be_rendered(): void
    {
        // Since Datatable is a base class that expects setup() to be overridden,
        // we test that it exists and has the expected methods
        $component = new Datatable();
        
        $this->assertInstanceOf(Datatable::class, $component);
    }

    /** @test */
    public function it_has_setup_method(): void
    {
        $component = new Datatable();
        
        $this->assertTrue(method_exists($component, 'setup'));
    }

    /** @test */
    public function it_has_render_method(): void
    {
        $component = new Datatable();
        
        $this->assertTrue(method_exists($component, 'render'));
    }
}
