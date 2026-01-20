<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Traits;

use Arkhas\LivewireDatatable\Traits\WithDatatable;
use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Columns\Column;
use Arkhas\LivewireDatatable\Columns\ActionColumn;
use Arkhas\LivewireDatatable\Actions\ColumnAction;
use Arkhas\LivewireDatatable\Actions\ColumnActionGroup;
use Arkhas\LivewireDatatable\Actions\TableAction;
use Arkhas\LivewireDatatable\Filters\Filter;
use Arkhas\LivewireDatatable\Filters\FilterOption;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;
use Livewire\Livewire;
use Livewire\Component;

class TestDatatableComponent extends Component
{
    use WithDatatable;

    public function setup(): void
    {
        $this->table(
            (new EloquentTable(TestModel::query()))
                ->columns([
                    Column::make('name')->sortable(),
                    Column::make('email'),
                    Column::make('status'),
                    ActionColumn::make()
                        ->action(
                            ColumnActionGroup::make()
                                ->actions([
                                    ColumnAction::make('edit')
                                        ->handle(fn($model) => ['success' => true]),
                                    ColumnAction::make('delete')
                                        ->handle(fn($model) => ['success' => true])
                                        ->confirm(fn($model) => ['title' => 'Delete?', 'message' => 'Are you sure?']),
                                ])
                        ),
                ])
                ->filters([
                    Filter::make('status')
                        ->multiple()
                        ->options([
                            FilterOption::make('active')
                                ->query(fn($q) => $q->where('status', 'active')),
                            FilterOption::make('inactive')
                                ->query(fn($q) => $q->where('status', 'inactive')),
                        ]),
                ])
                ->actions([
                    TableAction::make('bulk_delete')
                        ->handle(fn($ids) => ['success' => true, 'deleted' => count($ids)])
                        ->confirm(fn($ids) => ['title' => 'Delete?', 'message' => 'Delete ' . count($ids) . ' items?']),
                ])
                ->searchable(['name', 'email'])
        );
    }

    public function render()
    {
        return view('livewire-datatable::datatable', [
            'table' => $this->getTable(),
            'data' => $this->getData(),
        ]);
    }
}

class WithDatatableTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        parent::defineDatabaseMigrations();
    }

    protected function setUp(): void
    {
        parent::setUp();
        Livewire::component('test-datatable', TestDatatableComponent::class);
    }

    /** @test */
    public function it_mounts_with_default_values(): void
    {
        $this->defineDatabaseMigrations();
        
        Livewire::test(TestDatatableComponent::class)
            ->assertSet('search', null)
            ->assertSet('filters', [])
            ->assertSet('sortColumn', null)
            ->assertSet('sortDirection', 'asc')
            ->assertSet('perPage', 10)
            ->assertSet('hiddenColumns', []);
    }

    /** @test */
    public function it_can_get_table(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class);

        $this->assertInstanceOf(EloquentTable::class, $component->instance()->getTable());
    }

    /** @test */
    public function it_can_get_paginated_data(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
        TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

        $component = Livewire::test(TestDatatableComponent::class);
        $data = $component->instance()->getData();

        $this->assertEquals(2, $data->total());
    }

    /** @test */
    public function it_can_sort_by_column(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'Zebra', 'email' => 'z@example.com']);
        TestModel::create(['name' => 'Apple', 'email' => 'a@example.com']);

        $component = Livewire::test(TestDatatableComponent::class)
            ->call('sortBy', 'name');

        $this->assertEquals('name', $component->get('sortColumn'));
        $this->assertEquals('asc', $component->get('sortDirection'));
    }

    /** @test */
    public function it_toggles_sort_direction(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('sortBy', 'name')
            ->call('sortBy', 'name');

        $this->assertEquals('desc', $component->get('sortDirection'));
    }

    /** @test */
    public function it_resets_sort_direction_for_new_column(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('sortBy', 'name')
            ->call('sortBy', 'name')
            ->call('sortBy', 'email');

        $this->assertEquals('email', $component->get('sortColumn'));
        $this->assertEquals('asc', $component->get('sortDirection'));
    }

    /** @test */
    public function it_can_apply_filter(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('applyFilter', 'status', ['active']);

        $this->assertEquals(['status' => ['active']], $component->get('filters'));
    }

    /** @test */
    public function it_can_remove_filter(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('applyFilter', 'status', ['active'])
            ->call('removeFilter', 'status');

        $this->assertArrayNotHasKey('status', $component->get('filters'));
    }

    /** @test */
    public function it_can_reset_all_filters(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('applyFilter', 'status', ['active'])
            ->call('applyFilter', 'category', ['tech'])
            ->call('resetFilters');

        $this->assertEquals([], $component->get('filters'));
    }

    /** @test */
    public function it_can_toggle_filter_value(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('toggleFilter', 'status', 'active');

        $this->assertEquals(['status' => ['active']], $component->get('filters'));
    }

    /** @test */
    public function it_removes_filter_value_on_second_toggle(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('toggleFilter', 'status', 'active')
            ->call('toggleFilter', 'status', 'active');

        $this->assertEquals(['status' => []], $component->get('filters'));
    }

    /** @test */
    public function it_supports_multiple_filter_values(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('toggleFilter', 'status', 'active')
            ->call('toggleFilter', 'status', 'inactive');

        $this->assertEquals(['status' => ['active', 'inactive']], $component->get('filters'));
    }

    /** @test */
    public function it_replaces_value_for_single_select_filter(): void
    {
        $this->defineDatabaseMigrations();
        
        // Create a component with single-select filter
        $component = new class extends Component {
            use WithDatatable;

            public function setup(): void
            {
                $this->table(
                    (new EloquentTable(TestModel::query()))
                        ->columns([Column::make('name')])
                        ->filters([
                            Filter::make('status')
                                ->multiple(false)
                                ->options([
                                    FilterOption::make('active'),
                                    FilterOption::make('inactive'),
                                ]),
                        ])
                );
            }

            public function render()
            {
                return view('livewire-datatable::datatable', [
                    'table' => $this->getTable(),
                    'data' => $this->getData(),
                ]);
            }
        };

        Livewire::test($component::class)
            ->call('toggleFilter', 'status', 'active')
            ->call('toggleFilter', 'status', 'inactive')
            ->assertSet('filters.status', ['inactive']);
    }

    /** @test */
    public function it_can_toggle_column_visibility(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('toggleColumn', 'email');

        $this->assertContains('email', $component->get('hiddenColumns'));
        $this->assertFalse($component->instance()->isColumnVisible('email'));
    }

    /** @test */
    public function it_can_show_hidden_column(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('toggleColumn', 'email')
            ->call('toggleColumn', 'email');

        $this->assertNotContains('email', $component->get('hiddenColumns'));
        $this->assertTrue($component->instance()->isColumnVisible('email'));
    }

    /** @test */
    public function it_can_execute_bulk_action_with_ids(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
        TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

        $component = Livewire::test(TestDatatableComponent::class)
            ->call('executeBulkActionWithIds', 'bulk_delete', [1, 2]);

        // Action requires confirmation, so modal should show
        $this->assertTrue($component->get('showConfirmModal'));
        $this->assertEquals('bulk_delete', $component->get('pendingAction'));
    }

    /** @test */
    public function it_does_nothing_with_empty_ids(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('executeBulkActionWithIds', 'bulk_delete', []);

        $this->assertFalse($component->get('showConfirmModal'));
    }

    /** @test */
    public function it_does_nothing_for_missing_action(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('executeBulkActionWithIds', 'unknown_action', [1, 2]);

        $this->assertFalse($component->get('showConfirmModal'));
    }

    /** @test */
    public function it_can_execute_row_action(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create(['name' => 'User', 'email' => 'user@example.com']);

        $component = Livewire::test(TestDatatableComponent::class)
            ->call('executeRowAction', 'edit', $model->id);

        // edit action doesn't require confirmation
        $this->assertFalse($component->get('showConfirmModal'));
    }

    /** @test */
    public function it_shows_confirmation_for_row_action(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create(['name' => 'User', 'email' => 'user@example.com']);

        $component = Livewire::test(TestDatatableComponent::class)
            ->call('executeRowAction', 'delete', $model->id);

        $this->assertTrue($component->get('showConfirmModal'));
        $this->assertEquals('delete', $component->get('pendingAction'));
        $this->assertEquals('row', $component->get('pendingActionType'));
    }

    /** @test */
    public function it_does_nothing_for_missing_row(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('executeRowAction', 'edit', 999);

        $this->assertFalse($component->get('showConfirmModal'));
    }

    /** @test */
    public function it_can_confirm_and_execute_pending_action(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
        TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

        $component = Livewire::test(TestDatatableComponent::class)
            ->call('executeBulkActionWithIds', 'bulk_delete', [1, 2])
            ->call('confirmAction');

        $this->assertFalse($component->get('showConfirmModal'));
        $this->assertNull($component->get('pendingAction'));
    }

    /** @test */
    public function it_can_cancel_confirmation(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User', 'email' => 'user@example.com']);

        $component = Livewire::test(TestDatatableComponent::class)
            ->call('executeBulkActionWithIds', 'bulk_delete', [1])
            ->call('cancelConfirm');

        $this->assertFalse($component->get('showConfirmModal'));
        $this->assertNull($component->get('pendingAction'));
        $this->assertNull($component->get('pendingActionType'));
        $this->assertNull($component->get('pendingActionTarget'));
        $this->assertEquals([], $component->get('confirmData'));
    }

    /** @test */
    public function it_cancels_when_confirming_without_pending_action(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('confirmAction');

        $this->assertFalse($component->get('showConfirmModal'));
    }

    /** @test */
    public function it_resets_page_when_search_updates(): void
    {
        $this->defineDatabaseMigrations();
        
        for ($i = 1; $i <= 15; $i++) {
            TestModel::create(['name' => "User $i", 'email' => "user$i@example.com"]);
        }

        $component = Livewire::test(TestDatatableComponent::class)
            ->set('search', 'test');

        // Page should be reset to 1
        $this->assertEquals(1, $component->instance()->getData()->currentPage());
    }

    /** @test */
    public function it_resets_page_when_per_page_updates(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->set('perPage', 25);

        // Should not error
        $this->assertEquals(25, $component->get('perPage'));
    }

    /** @test */
    public function it_can_get_active_filters_count(): void
    {
        $this->defineDatabaseMigrations();
        
        $component = Livewire::test(TestDatatableComponent::class)
            ->call('applyFilter', 'status', ['active']);

        $this->assertEquals(1, $component->instance()->getActiveFiltersCount());
    }
}
