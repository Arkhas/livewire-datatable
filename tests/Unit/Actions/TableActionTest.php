<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Actions;

use Arkhas\LivewireDatatable\Actions\TableAction;
use Arkhas\LivewireDatatable\Tests\TestCase;

class TableActionTest extends TestCase
{
    /** @test */
    public function it_can_be_created_with_make(): void
    {
        $action = TableAction::make('delete');

        $this->assertInstanceOf(TableAction::class, $action);
        $this->assertEquals('delete', $action->getName());
    }

    /** @test */
    public function it_can_be_created_with_constructor(): void
    {
        $action = new TableAction('export');

        $this->assertEquals('export', $action->getName());
    }

    /** @test */
    public function it_generates_label_from_name_if_not_set(): void
    {
        $action = TableAction::make('delete_all');

        $this->assertEquals('Delete all', $action->getLabel());
    }

    /** @test */
    public function it_can_set_label(): void
    {
        $action = TableAction::make('delete')
            ->label('Delete Selected');

        $this->assertEquals('Delete Selected', $action->getLabel());
    }

    /** @test */
    public function it_can_set_icon_with_default_position(): void
    {
        $action = TableAction::make('delete')
            ->icon('trash');

        $this->assertEquals('trash', $action->getIcon());
        $this->assertEquals('left', $action->getIconPosition());
    }

    /** @test */
    public function it_can_set_icon_with_custom_position(): void
    {
        $action = TableAction::make('export')
            ->icon('download', 'right');

        $this->assertEquals('download', $action->getIcon());
        $this->assertEquals('right', $action->getIconPosition());
    }

    /** @test */
    public function it_returns_null_icon_by_default(): void
    {
        $action = TableAction::make('delete');

        $this->assertNull($action->getIcon());
    }

    /** @test */
    public function it_returns_left_icon_position_by_default(): void
    {
        $action = TableAction::make('delete');

        $this->assertEquals('left', $action->getIconPosition());
    }

    /** @test */
    public function it_can_set_props(): void
    {
        $action = TableAction::make('delete')
            ->props(['variant' => 'danger', 'size' => 'sm']);

        $this->assertEquals(['variant' => 'danger', 'size' => 'sm'], $action->getProps());
    }

    /** @test */
    public function it_returns_empty_props_by_default(): void
    {
        $action = TableAction::make('delete');

        $this->assertEquals([], $action->getProps());
    }

    /** @test */
    public function it_can_set_styles(): void
    {
        $action = TableAction::make('delete')
            ->styles('color: red; font-weight: bold;');

        $this->assertEquals('color: red; font-weight: bold;', $action->getStyles());
    }

    /** @test */
    public function it_returns_null_styles_by_default(): void
    {
        $action = TableAction::make('delete');

        $this->assertNull($action->getStyles());
    }

    /** @test */
    public function it_can_set_handler(): void
    {
        $action = TableAction::make('delete')
            ->handle(fn($ids) => ['success' => true, 'deleted' => count($ids)]);

        $this->assertInstanceOf(TableAction::class, $action);
    }

    /** @test */
    public function it_can_execute_action_with_handler(): void
    {
        $action = TableAction::make('delete')
            ->handle(fn($ids) => [
                'success' => true,
                'message' => 'Deleted ' . count($ids) . ' items',
            ]);

        $result = $action->execute([1, 2, 3]);

        $this->assertTrue($result['success']);
        $this->assertEquals('Deleted 3 items', $result['message']);
    }

    /** @test */
    public function it_returns_error_when_executing_without_handler(): void
    {
        $action = TableAction::make('delete');

        $result = $action->execute([1, 2, 3]);

        $this->assertFalse($result['success']);
        $this->assertEquals('No handler defined', $result['message']);
    }

    /** @test */
    public function it_can_set_confirmation(): void
    {
        $action = TableAction::make('delete')
            ->confirm(fn($ids) => [
                'title' => 'Delete Items?',
                'message' => 'Are you sure you want to delete ' . count($ids) . ' items?',
            ]);

        $this->assertTrue($action->requiresConfirmation());
    }

    /** @test */
    public function it_does_not_require_confirmation_by_default(): void
    {
        $action = TableAction::make('delete');

        $this->assertFalse($action->requiresConfirmation());
    }

    /** @test */
    public function it_can_get_confirmation_data(): void
    {
        $action = TableAction::make('delete')
            ->confirm(fn($ids) => [
                'title' => 'Confirm Delete',
                'message' => 'Delete ' . count($ids) . ' selected items?',
            ]);

        $confirmation = $action->getConfirmation([1, 2, 3, 4, 5]);

        $this->assertEquals('Confirm Delete', $confirmation['title']);
        $this->assertEquals('Delete 5 selected items?', $confirmation['message']);
    }

    /** @test */
    public function it_returns_null_confirmation_when_not_set(): void
    {
        $action = TableAction::make('delete');

        $this->assertNull($action->getConfirmation([1, 2, 3]));
    }

    /** @test */
    public function it_can_convert_to_array(): void
    {
        $action = TableAction::make('delete')
            ->label('Delete Selected')
            ->icon('trash', 'left')
            ->props(['variant' => 'danger'])
            ->styles('font-weight: bold;')
            ->confirm(fn($ids) => ['title' => 'Confirm']);

        $array = $action->toArray();

        $this->assertEquals('delete', $array['name']);
        $this->assertEquals('Delete Selected', $array['label']);
        $this->assertEquals('trash', $array['icon']);
        $this->assertEquals('left', $array['iconPosition']);
        $this->assertEquals(['variant' => 'danger'], $array['props']);
        $this->assertEquals('font-weight: bold;', $array['styles']);
        $this->assertTrue($array['requiresConfirmation']);
        $this->assertEquals('action', $array['type']);
    }

    /** @test */
    public function it_supports_fluent_api(): void
    {
        $action = TableAction::make('bulk_delete')
            ->label('Delete All')
            ->icon('trash-2', 'right')
            ->props(['variant' => 'destructive'])
            ->styles('margin-left: 8px;')
            ->handle(fn($ids) => ['success' => true])
            ->confirm(fn($ids) => ['title' => 'Confirm']);

        $this->assertInstanceOf(TableAction::class, $action);
        $this->assertEquals('Delete All', $action->getLabel());
        $this->assertEquals('trash-2', $action->getIcon());
        $this->assertEquals('right', $action->getIconPosition());
        $this->assertEquals(['variant' => 'destructive'], $action->getProps());
        $this->assertEquals('margin-left: 8px;', $action->getStyles());
        $this->assertTrue($action->requiresConfirmation());
    }
}
