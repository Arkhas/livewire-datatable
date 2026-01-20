<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Table\Concerns;

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Actions\TableAction;
use Arkhas\LivewireDatatable\Actions\TableActionGroup;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

class HasActionsTest extends TestCase
{
    protected function createTable(): EloquentTable
    {
        return new EloquentTable(TestModel::query());
    }

    /** @test */
    public function it_can_set_actions(): void
    {
        $actions = [
            TableAction::make('delete'),
            TableAction::make('archive'),
        ];

        $table = $this->createTable()
            ->actions($actions);

        $this->assertSame($actions, $table->getActions());
    }

    /** @test */
    public function it_returns_empty_actions_by_default(): void
    {
        $table = $this->createTable();

        $this->assertEquals([], $table->getActions());
    }

    /** @test */
    public function it_can_get_action_by_name(): void
    {
        $deleteAction = TableAction::make('delete');
        $archiveAction = TableAction::make('archive');

        $table = $this->createTable()
            ->actions([$deleteAction, $archiveAction]);

        $this->assertSame($deleteAction, $table->getAction('delete'));
        $this->assertSame($archiveAction, $table->getAction('archive'));
    }

    /** @test */
    public function it_returns_null_for_missing_action(): void
    {
        $table = $this->createTable()
            ->actions([TableAction::make('delete')]);

        $this->assertNull($table->getAction('archive'));
    }

    /** @test */
    public function it_can_find_action_in_group(): void
    {
        $nestedAction = TableAction::make('nested_delete')
            ->handle(fn($ids) => ['success' => true]);

        $table = $this->createTable()
            ->actions([
                TableAction::make('delete'),
                TableActionGroup::make('more')
                    ->actions([
                        $nestedAction,
                        TableAction::make('archive'),
                    ]),
            ]);

        $this->assertSame($nestedAction, $table->getAction('nested_delete'));
    }

    /** @test */
    public function it_can_execute_action(): void
    {
        $table = $this->createTable()
            ->actions([
                TableAction::make('delete')
                    ->handle(fn($ids) => [
                        'success' => true,
                        'count' => count($ids),
                    ]),
            ]);

        $result = $table->executeAction('delete', [1, 2, 3]);

        $this->assertTrue($result['success']);
        $this->assertEquals(3, $result['count']);
    }

    /** @test */
    public function it_returns_error_for_missing_action_execution(): void
    {
        $table = $this->createTable()
            ->actions([TableAction::make('delete')]);

        $result = $table->executeAction('unknown', [1, 2, 3]);

        $this->assertFalse($result['success']);
        $this->assertEquals('Action not found', $result['message']);
    }

    /** @test */
    public function it_can_execute_nested_action(): void
    {
        $table = $this->createTable()
            ->actions([
                TableActionGroup::make('more')
                    ->actions([
                        TableAction::make('archive')
                            ->handle(fn($ids) => ['success' => true, 'archived' => count($ids)]),
                    ]),
            ]);

        $result = $table->executeAction('archive', [1, 2]);

        $this->assertTrue($result['success']);
        $this->assertEquals(2, $result['archived']);
    }
}
