<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Columns;

use Arkhas\LivewireDatatable\Columns\ActionColumn;
use Arkhas\LivewireDatatable\Actions\ColumnAction;
use Arkhas\LivewireDatatable\Actions\ColumnActionGroup;
use Arkhas\LivewireDatatable\Tests\TestCase;

class ActionColumnTest extends TestCase
{
    /** @test */
    public function it_can_be_created_with_make(): void
    {
        $column = ActionColumn::make();

        $this->assertInstanceOf(ActionColumn::class, $column);
    }

    /** @test */
    public function it_has_default_name(): void
    {
        $column = ActionColumn::make();

        $this->assertEquals('__actions', $column->getName());
    }

    /** @test */
    public function it_can_have_custom_name(): void
    {
        $column = ActionColumn::make('custom_actions');

        $this->assertEquals('custom_actions', $column->getName());
    }

    /** @test */
    public function it_is_not_sortable_by_default(): void
    {
        $column = ActionColumn::make();

        $this->assertFalse($column->isSortable());
    }

    /** @test */
    public function it_is_not_toggable_by_default(): void
    {
        $column = ActionColumn::make();

        $this->assertFalse($column->isToggable());
    }

    /** @test */
    public function it_has_empty_label_by_default(): void
    {
        $column = ActionColumn::make();

        $this->assertEquals('', $column->getLabel());
    }

    /** @test */
    public function it_can_set_column_action(): void
    {
        $action = ColumnAction::make('edit')
            ->label('Edit');

        $column = ActionColumn::make()
            ->action($action);

        $this->assertSame($action, $column->getAction());
    }

    /** @test */
    public function it_can_set_column_action_group(): void
    {
        $group = ColumnActionGroup::make()
            ->actions([
                ColumnAction::make('edit'),
                ColumnAction::make('delete'),
            ]);

        $column = ActionColumn::make()
            ->action($group);

        $this->assertSame($group, $column->getAction());
    }

    /** @test */
    public function it_returns_null_action_by_default(): void
    {
        $column = ActionColumn::make();

        $this->assertNull($column->getAction());
    }

    /** @test */
    public function it_can_be_converted_to_array(): void
    {
        $action = ColumnAction::make('edit')
            ->label('Edit');

        $column = ActionColumn::make()
            ->action($action);

        $array = $column->toArray();

        $this->assertEquals('action', $array['type']);
        $this->assertArrayHasKey('action', $array);
        $this->assertEquals('edit', $array['action']['name']);
    }

    /** @test */
    public function it_converts_to_array_with_null_action(): void
    {
        $column = ActionColumn::make();

        $array = $column->toArray();

        $this->assertEquals('action', $array['type']);
        $this->assertNull($array['action']);
    }

    /** @test */
    public function it_inherits_column_methods(): void
    {
        $column = ActionColumn::make()
            ->width('50px')
            ->hidden();

        $this->assertEquals('50px', $column->getWidth());
        $this->assertTrue($column->isHidden());
    }
}
