<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Facades;

use Arkhas\LivewireDatatable\Facades\LivewireDatatable;
use Arkhas\LivewireDatatable\Tests\TestCase;

class LivewireDatatableFacadeTest extends TestCase
{
    /** @test */
    public function it_has_facade_accessor(): void
    {
        // The facade should return the accessor name
        $reflection = new \ReflectionClass(LivewireDatatable::class);
        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);
        
        $accessor = $method->invoke(null);
        
        $this->assertEquals('livewire-datatable', $accessor);
    }
}
