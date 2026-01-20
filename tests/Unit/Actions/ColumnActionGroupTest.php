<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Actions;

use Arkhas\LivewireDatatable\Actions\ColumnAction;
use Arkhas\LivewireDatatable\Actions\ColumnActionGroup;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

class ColumnActionGroupTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        parent::defineDatabaseMigrations();
    }

    /** @test */
    public function it_can_be_created_with_make(): void
    {
        $group = ColumnActionGroup::make();

        $this->assertInstanceOf(ColumnActionGroup::class, $group);
    }

    /** @test */
    public function it_can_set_icon(): void
    {
        $group = ColumnActionGroup::make()
            ->icon('ellipsis-vertical');

        $this->assertEquals('ellipsis-vertical', $group->getIcon());
    }

    /** @test */
    public function it_returns_null_icon_by_default(): void
    {
        $group = ColumnActionGroup::make();

        $this->assertNull($group->getIcon());
    }

    /** @test */
    public function it_can_set_actions(): void
    {
        $actions = [
            ColumnAction::make('edit'),
            ColumnAction::make('delete'),
        ];

        $group = ColumnActionGroup::make()
            ->actions($actions);

        $this->assertCount(2, $group->getActions());
        $this->assertSame($actions, $group->getActions());
    }

    /** @test */
    public function it_returns_empty_actions_by_default(): void
    {
        $group = ColumnActionGroup::make();

        $this->assertEquals([], $group->getActions());
    }

    /** @test */
    public function it_can_get_action_by_name(): void
    {
        $editAction = ColumnAction::make('edit');
        $deleteAction = ColumnAction::make('delete');

        $group = ColumnActionGroup::make()
            ->actions([$editAction, $deleteAction]);

        $this->assertSame($editAction, $group->getAction('edit'));
        $this->assertSame($deleteAction, $group->getAction('delete'));
    }

    /** @test */
    public function it_returns_null_for_missing_action(): void
    {
        $group = ColumnActionGroup::make()
            ->actions([
                ColumnAction::make('edit'),
            ]);

        $this->assertNull($group->getAction('delete'));
    }

    /** @test */
    public function it_can_convert_to_array_for_model(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $group = ColumnActionGroup::make()
            ->icon('dots')
            ->actions([
                ColumnAction::make('edit')
                    ->label(fn($model) => "Edit {$model->name}"),
                ColumnAction::make('delete')
                    ->label('Delete'),
            ]);

        $array = $group->toArrayForModel($model);

        $this->assertEquals('dots', $array['icon']);
        $this->assertEquals('group', $array['type']);
        $this->assertCount(2, $array['actions']);
        $this->assertEquals('Edit Test', $array['actions'][0]['label']);
        $this->assertEquals('Delete', $array['actions'][1]['label']);
    }

    /** @test */
    public function it_can_convert_to_array(): void
    {
        $group = ColumnActionGroup::make()
            ->icon('menu')
            ->actions([
                ColumnAction::make('edit')
                    ->label('Edit'),
                ColumnAction::make('delete')
                    ->label('Delete'),
            ]);

        $array = $group->toArray();

        $this->assertEquals('menu', $array['icon']);
        $this->assertEquals('group', $array['type']);
        $this->assertCount(2, $array['actions']);
        $this->assertEquals('edit', $array['actions'][0]['name']);
        $this->assertEquals('delete', $array['actions'][1]['name']);
    }

    /** @test */
    public function it_supports_fluent_api(): void
    {
        $group = ColumnActionGroup::make()
            ->icon('more')
            ->actions([
                ColumnAction::make('view'),
                ColumnAction::make('edit'),
                ColumnAction::make('delete'),
            ]);

        $this->assertInstanceOf(ColumnActionGroup::class, $group);
        $this->assertEquals('more', $group->getIcon());
        $this->assertCount(3, $group->getActions());
    }
}
