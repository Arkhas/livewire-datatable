<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Columns;

use Arkhas\LivewireDatatable\Columns\CheckboxColumn;
use Arkhas\LivewireDatatable\Tests\TestCase;

class CheckboxColumnTest extends TestCase
{
    /** @test */
    public function it_can_be_created_with_make(): void
    {
        $column = CheckboxColumn::make();

        $this->assertInstanceOf(CheckboxColumn::class, $column);
    }

    /** @test */
    public function it_has_default_name(): void
    {
        $column = CheckboxColumn::make();

        $this->assertEquals('__checkbox', $column->getName());
    }

    /** @test */
    public function it_ignores_custom_name_in_make(): void
    {
        $column = CheckboxColumn::make('custom');

        // CheckboxColumn always uses __checkbox internally
        $this->assertEquals('__checkbox', $column->getName());
    }

    /** @test */
    public function it_is_not_sortable_by_default(): void
    {
        $column = CheckboxColumn::make();

        $this->assertFalse($column->isSortable());
    }

    /** @test */
    public function it_is_not_toggable_by_default(): void
    {
        $column = CheckboxColumn::make();

        $this->assertFalse($column->isToggable());
    }

    /** @test */
    public function it_has_empty_label_by_default(): void
    {
        $column = CheckboxColumn::make();

        $this->assertEquals('', $column->getLabel());
    }

    /** @test */
    public function it_can_be_converted_to_array(): void
    {
        $column = CheckboxColumn::make();

        $array = $column->toArray();

        $this->assertEquals('checkbox', $array['type']);
        $this->assertEquals('__checkbox', $array['name']);
        $this->assertEquals('', $array['label']);
        $this->assertFalse($array['sortable']);
        $this->assertFalse($array['toggable']);
    }

    /** @test */
    public function it_inherits_column_methods(): void
    {
        $column = CheckboxColumn::make()
            ->width('40px')
            ->hidden();

        $this->assertEquals('40px', $column->getWidth());
        $this->assertTrue($column->isHidden());
    }
}
