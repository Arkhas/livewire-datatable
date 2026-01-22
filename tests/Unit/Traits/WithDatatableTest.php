<?php

use Arkhas\LivewireDatatable\Traits\WithDatatable;
use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Columns\Column;
use Arkhas\LivewireDatatable\Columns\ActionColumn;
use Arkhas\LivewireDatatable\Actions\ColumnAction;
use Arkhas\LivewireDatatable\Actions\ColumnActionGroup;
use Arkhas\LivewireDatatable\Actions\TableAction;
use Arkhas\LivewireDatatable\Filters\Filter;
use Arkhas\LivewireDatatable\Filters\FilterOption;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;
use Livewire\Livewire;
use Livewire\Component;

// Main test component
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

// Component with direct ColumnAction (not in group)
class TestDirectActionComponent extends Component
{
    use WithDatatable;

    public function setup(): void
    {
        $this->table(
            (new EloquentTable(TestModel::query()))
                ->columns([
                    Column::make('name'),
                    ActionColumn::make()
                        ->action(
                            ColumnAction::make('edit')
                                ->handle(fn($model) => ['success' => true, 'message' => 'Edited'])
                        ),
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
}

// Component with bulk action without confirmation
class TestBulkActionNoConfirmComponent extends Component
{
    use WithDatatable;

    public function setup(): void
    {
        $this->table(
            (new EloquentTable(TestModel::query()))
                ->columns([Column::make('name')])
                ->actions([
                    TableAction::make('bulk_edit')
                        ->handle(fn($ids) => ['success' => true, 'message' => 'Bulk edited']),
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
}

// Component for export testing
class TestExportComponent extends Component
{
    use WithDatatable;

    public function setup(): void
    {
        $this->table(
            (new EloquentTable(TestModel::query()))
                ->columns([Column::make('name')])
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

beforeEach(function () {
    Livewire::component('test-datatable', TestDatatableComponent::class);
    Livewire::component('test-direct-action', TestDirectActionComponent::class);
    Livewire::component('test-bulk-no-confirm', TestBulkActionNoConfirmComponent::class);
    Livewire::component('test-export', TestExportComponent::class);
});

test('it mounts with default values', function () {
    Livewire::test(TestDatatableComponent::class)
        ->assertSet('search', null)
        ->assertSet('filters', [])
        ->assertSet('sortColumn', null)
        ->assertSet('sortDirection', 'asc')
        ->assertSet('perPage', 10)
        ->assertSet('hiddenColumns', []);
});

test('it can get table', function () {
    $component = Livewire::test(TestDatatableComponent::class);

    expect($component->instance()->getTable())->toBeInstanceOf(EloquentTable::class);
});

test('it can get paginated data', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

    $component = Livewire::test(TestDatatableComponent::class);
    $data = $component->instance()->getData();

    expect($data->total())->toBe(2);
});

test('it can sort by column', function () {
    TestModel::create(['name' => 'Zebra', 'email' => 'z@example.com']);
    TestModel::create(['name' => 'Apple', 'email' => 'a@example.com']);

    $component = Livewire::test(TestDatatableComponent::class)
        ->call('sortBy', 'name');

    expect($component->get('sortColumn'))->toBe('name')
        ->and($component->get('sortDirection'))->toBe('asc');
});

test('it toggles sort direction', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('sortBy', 'name')
        ->call('sortBy', 'name');

    expect($component->get('sortDirection'))->toBe('desc');
});

test('it resets sort direction for new column', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('sortBy', 'name')
        ->call('sortBy', 'name')
        ->call('sortBy', 'email');

    expect($component->get('sortColumn'))->toBe('email')
        ->and($component->get('sortDirection'))->toBe('asc');
});

test('it can apply filter', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('applyFilter', 'status', ['active']);

    expect($component->get('filters'))->toBe(['status' => ['active']]);
});

test('it can remove filter', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('applyFilter', 'status', ['active'])
        ->call('removeFilter', 'status');

    expect($component->get('filters'))->not->toHaveKey('status');
});

test('it can reset all filters', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('applyFilter', 'status', ['active'])
        ->call('applyFilter', 'category', ['tech'])
        ->call('resetFilters');

    expect($component->get('filters'))->toBe([]);
});

test('it can toggle filter value', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('toggleFilter', 'status', 'active');

    expect($component->get('filters'))->toBe(['status' => ['active']]);
});

test('it removes filter value on second toggle', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('toggleFilter', 'status', 'active')
        ->call('toggleFilter', 'status', 'active');

    expect($component->get('filters'))->toBe(['status' => []]);
});

test('it supports multiple filter values', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('toggleFilter', 'status', 'active')
        ->call('toggleFilter', 'status', 'inactive');

    expect($component->get('filters'))->toBe(['status' => ['active', 'inactive']]);
});

test('it replaces value for single select filter', function () {
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

    Livewire::component('test-single-filter', $component::class);

    Livewire::test($component::class)
        ->call('toggleFilter', 'status', 'active')
        ->call('toggleFilter', 'status', 'inactive')
        ->assertSet('filters.status', ['inactive']);
});

test('it can toggle column visibility', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('toggleColumn', 'email');

    expect($component->get('hiddenColumns'))->toContain('email')
        ->and($component->instance()->isColumnVisible('email'))->toBeFalse();
});

test('it can show hidden column', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('toggleColumn', 'email')
        ->call('toggleColumn', 'email');

    expect($component->get('hiddenColumns'))->not->toContain('email')
        ->and($component->instance()->isColumnVisible('email'))->toBeTrue();
});

test('it can execute bulk action with ids', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

    $component = Livewire::test(TestDatatableComponent::class)
        ->call('executeBulkActionWithIds', 'bulk_delete', [1, 2]);

    // Action requires confirmation, so modal should show
    expect($component->get('showConfirmModal'))->toBeTrue()
        ->and($component->get('pendingAction'))->toBe('bulk_delete');
});

test('it does nothing with empty ids', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('executeBulkActionWithIds', 'bulk_delete', []);

    expect($component->get('showConfirmModal'))->toBeFalse();
});

test('it does nothing for missing action', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('executeBulkActionWithIds', 'unknown_action', [1, 2]);

    expect($component->get('showConfirmModal'))->toBeFalse();
});

test('it can execute row action', function () {
    $model = TestModel::create(['name' => 'User', 'email' => 'user@example.com']);

    $component = Livewire::test(TestDatatableComponent::class)
        ->call('executeRowAction', 'edit', $model->id);

    // edit action doesn't require confirmation
    expect($component->get('showConfirmModal'))->toBeFalse();
});

test('it shows confirmation for row action', function () {
    $model = TestModel::create(['name' => 'User', 'email' => 'user@example.com']);

    $component = Livewire::test(TestDatatableComponent::class)
        ->call('executeRowAction', 'delete', $model->id);

    expect($component->get('showConfirmModal'))->toBeTrue()
        ->and($component->get('pendingAction'))->toBe('delete')
        ->and($component->get('pendingActionType'))->toBe('row');
});

test('it does nothing for missing row', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('executeRowAction', 'edit', 999);

    expect($component->get('showConfirmModal'))->toBeFalse();
});

test('it can confirm and execute pending action', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

    $component = Livewire::test(TestDatatableComponent::class)
        ->call('executeBulkActionWithIds', 'bulk_delete', [1, 2])
        ->call('confirmAction');

    expect($component->get('showConfirmModal'))->toBeFalse()
        ->and($component->get('pendingAction'))->toBeNull();
});

test('it can cancel confirmation', function () {
    TestModel::create(['name' => 'User', 'email' => 'user@example.com']);

    $component = Livewire::test(TestDatatableComponent::class)
        ->call('executeBulkActionWithIds', 'bulk_delete', [1])
        ->call('cancelConfirm');

    expect($component->get('showConfirmModal'))->toBeFalse()
        ->and($component->get('pendingAction'))->toBeNull()
        ->and($component->get('pendingActionType'))->toBeNull()
        ->and($component->get('pendingActionTarget'))->toBeNull()
        ->and($component->get('confirmData'))->toBe([]);
});

test('it cancels when confirming without pending action', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('confirmAction');

    expect($component->get('showConfirmModal'))->toBeFalse();
});

test('it resets page when search updates', function () {
    for ($i = 1; $i <= 15; $i++) {
        TestModel::create(['name' => "User $i", 'email' => "user$i@example.com"]);
    }

    $component = Livewire::test(TestDatatableComponent::class)
        ->set('search', 'test');

    // Page should be reset to 1
    expect($component->instance()->getData()->currentPage())->toBe(1);
});

test('it resets page when per page updates', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->set('perPage', 25);

    // Should not error
    expect($component->get('perPage'))->toBe(25);
});

test('it can get active filters count', function () {
    $component = Livewire::test(TestDatatableComponent::class)
        ->call('applyFilter', 'status', ['active']);

    expect($component->instance()->getActiveFiltersCount())->toBe(1);
});

// Additional tests from WithDatatableAdditionalTest

test('it can execute row action with direct ColumnAction', function () {
    $model = TestModel::create(['name' => 'Test', 'email' => 'test@example.com']);

    $component = Livewire::test(TestDirectActionComponent::class)
        ->call('executeRowAction', 'edit', $model->id);

    expect($component->get('showConfirmModal'))->toBeFalse();
});

test('it returns nothing when row action not found', function () {
    $model = TestModel::create(['name' => 'Test', 'email' => 'test@example.com']);

    $component = Livewire::test(TestDirectActionComponent::class)
        ->call('executeRowAction', 'unknown', $model->id);

    expect($component->get('showConfirmModal'))->toBeFalse();
});

test('it can execute bulk action without confirmation', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

    $component = Livewire::test(TestBulkActionNoConfirmComponent::class)
        ->call('executeBulkActionWithIds', 'bulk_edit', [1, 2]);

    expect($component->get('showConfirmModal'))->toBeFalse();
});

test('it handles action result with success and message', function () {
    $model = TestModel::create(['name' => 'Test', 'email' => 'test@example.com']);

    // Should not throw error
    Livewire::test(TestDirectActionComponent::class)
        ->call('executeRowAction', 'edit', $model->id);

    expect(true)->toBeTrue();
});

test('it handles action result with error message', function () {
    $component = new class extends Component {
        use WithDatatable;

        public function setup(): void
        {
            $this->table(
                (new EloquentTable(TestModel::query()))
                    ->columns([
                        Column::make('name'),
                        ActionColumn::make()
                            ->action(
                                ColumnAction::make('error_action')
                                    ->handle(fn($model) => ['success' => false, 'message' => 'Error occurred'])
                            ),
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

    Livewire::component('test-error-action', $component::class);

    $model = TestModel::create(['name' => 'Test', 'email' => 'test@example.com']);

    // Should not throw error
    Livewire::test($component::class)
        ->call('executeRowAction', 'error_action', $model->id);

    expect(true)->toBeTrue();
});

test('it can export data', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    // Call export method - it should not throw error
    Livewire::test(TestExportComponent::class)
        ->call('export', 'csv');

    expect(true)->toBeTrue();
});

test('it can export data with xlsx format', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

    $component = Livewire::test(TestExportComponent::class);

    if (!class_exists('Maatwebsite\Excel\Facades\Excel')) {
        expect(fn() => $component->call('export', 'xlsx'))
            ->toThrow(\RuntimeException::class);
    } else {
        $component->call('export', 'xlsx');
        expect(true)->toBeTrue();
    }
});

test('it can confirm and execute row action', function () {
    $model = TestModel::create(['name' => 'Test', 'email' => 'test@example.com']);

    $component = Livewire::test(TestDatatableComponent::class)
        ->call('executeRowAction', 'delete', $model->id)
        ->call('confirmAction');

    expect($component->get('showConfirmModal'))->toBeFalse()
        ->and($component->get('pendingAction'))->toBeNull();
});

test('it returns nothing when performRowAction model not found', function () {
    $component = new class extends Component {
        use WithDatatable;

        public function setup(): void
        {
            $this->table(
                (new EloquentTable(TestModel::query()))
                    ->columns([
                        Column::make('name'),
                        ActionColumn::make()
                            ->action(
                                ColumnAction::make('edit')
                                    ->handle(fn($model) => ['success' => true])
                            ),
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

    Livewire::component('test-missing-model', $component::class);

    $test = Livewire::test($component::class);
    
    // Use reflection to call protected method
    $reflection = new \ReflectionClass($test->instance());
    $method = $reflection->getMethod('performRowAction');
    $method->setAccessible(true);
    
    $method->invoke($test->instance(), 'edit', 99999);

    expect(true)->toBeTrue(); // Should not throw error
});
