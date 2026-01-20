<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Actions;

use Arkhas\LivewireDatatable\Actions\TableAction;
use Arkhas\LivewireDatatable\Actions\TableActionGroup;
use Arkhas\LivewireDatatable\Tests\TestCase;

class TableActionGroupTest extends TestCase
{
    /** @test */
    public function it_can_be_created_with_make(): void
    {
        $group = TableActionGroup::make('bulk_actions');

        $this->assertInstanceOf(TableActionGroup::class, $group);
        $this->assertEquals('bulk_actions', $group->getName());
    }

    /** @test */
    public function it_can_be_created_with_constructor(): void
    {
        $group = new TableActionGroup('actions');

        $this->assertEquals('actions', $group->getName());
    }

    /** @test */
    public function it_generates_label_from_name_if_not_set(): void
    {
        $group = TableActionGroup::make('bulk_actions');

        $this->assertEquals('Bulk actions', $group->getLabel());
    }

    /** @test */
    public function it_can_set_label(): void
    {
        $group = TableActionGroup::make('actions')
            ->label('More Actions');

        $this->assertEquals('More Actions', $group->getLabel());
    }

    /** @test */
    public function it_can_set_icon(): void
    {
        $group = TableActionGroup::make('actions')
            ->icon('chevron-down');

        $this->assertEquals('chevron-down', $group->getIcon());
    }

    /** @test */
    public function it_returns_null_icon_by_default(): void
    {
        $group = TableActionGroup::make('actions');

        $this->assertNull($group->getIcon());
    }

    /** @test */
    public function it_can_set_props(): void
    {
        $group = TableActionGroup::make('actions')
            ->props(['variant' => 'outline', 'size' => 'sm']);

        $this->assertEquals(['variant' => 'outline', 'size' => 'sm'], $group->getProps());
    }

    /** @test */
    public function it_returns_empty_props_by_default(): void
    {
        $group = TableActionGroup::make('actions');

        $this->assertEquals([], $group->getProps());
    }

    /** @test */
    public function it_can_set_styles(): void
    {
        $group = TableActionGroup::make('actions')
            ->styles('min-width: 150px;');

        $this->assertEquals('min-width: 150px;', $group->getStyles());
    }

    /** @test */
    public function it_returns_null_styles_by_default(): void
    {
        $group = TableActionGroup::make('actions');

        $this->assertNull($group->getStyles());
    }

    /** @test */
    public function it_can_set_actions(): void
    {
        $actions = [
            TableAction::make('delete'),
            TableAction::make('archive'),
        ];

        $group = TableActionGroup::make('bulk')
            ->actions($actions);

        $this->assertCount(2, $group->getActions());
        $this->assertSame($actions, $group->getActions());
    }

    /** @test */
    public function it_returns_empty_actions_by_default(): void
    {
        $group = TableActionGroup::make('actions');

        $this->assertEquals([], $group->getActions());
    }

    /** @test */
    public function it_can_get_action_by_name(): void
    {
        $deleteAction = TableAction::make('delete');
        $archiveAction = TableAction::make('archive');

        $group = TableActionGroup::make('bulk')
            ->actions([$deleteAction, $archiveAction]);

        $this->assertSame($deleteAction, $group->getAction('delete'));
        $this->assertSame($archiveAction, $group->getAction('archive'));
    }

    /** @test */
    public function it_returns_null_for_missing_action(): void
    {
        $group = TableActionGroup::make('bulk')
            ->actions([
                TableAction::make('delete'),
            ]);

        $this->assertNull($group->getAction('archive'));
    }

    /** @test */
    public function it_returns_error_when_executing_directly(): void
    {
        $group = TableActionGroup::make('bulk')
            ->actions([
                TableAction::make('delete')
                    ->handle(fn($ids) => ['success' => true]),
            ]);

        $result = $group->execute([1, 2, 3]);

        $this->assertFalse($result['success']);
        $this->assertEquals('Cannot execute action group directly', $result['message']);
    }

    /** @test */
    public function it_does_not_require_confirmation(): void
    {
        $group = TableActionGroup::make('bulk');

        $this->assertFalse($group->requiresConfirmation());
    }

    /** @test */
    public function it_can_convert_to_array(): void
    {
        $group = TableActionGroup::make('bulk_actions')
            ->label('Bulk Actions')
            ->icon('more-horizontal')
            ->props(['variant' => 'outline'])
            ->styles('margin-right: 4px;')
            ->actions([
                TableAction::make('delete')
                    ->label('Delete Selected'),
                TableAction::make('archive')
                    ->label('Archive Selected'),
            ]);

        $array = $group->toArray();

        $this->assertEquals('bulk_actions', $array['name']);
        $this->assertEquals('Bulk Actions', $array['label']);
        $this->assertEquals('more-horizontal', $array['icon']);
        $this->assertEquals(['variant' => 'outline'], $array['props']);
        $this->assertEquals('margin-right: 4px;', $array['styles']);
        $this->assertEquals('group', $array['type']);
        $this->assertCount(2, $array['actions']);
        $this->assertEquals('delete', $array['actions'][0]['name']);
        $this->assertEquals('archive', $array['actions'][1]['name']);
    }

    /** @test */
    public function it_supports_fluent_api(): void
    {
        $group = TableActionGroup::make('actions')
            ->label('Actions')
            ->icon('menu')
            ->props(['size' => 'sm'])
            ->styles('gap: 4px;')
            ->actions([
                TableAction::make('edit'),
                TableAction::make('delete'),
            ]);

        $this->assertInstanceOf(TableActionGroup::class, $group);
        $this->assertEquals('Actions', $group->getLabel());
        $this->assertEquals('menu', $group->getIcon());
        $this->assertEquals(['size' => 'sm'], $group->getProps());
        $this->assertEquals('gap: 4px;', $group->getStyles());
        $this->assertCount(2, $group->getActions());
    }
}
