# Livewire Datatable

A powerful datatable component for Laravel Livewire with filters, actions, and export capabilities. Built with Flux Pro components.

## Requirements

- PHP 8.1+
- Laravel 10.x or 11.x
- Livewire 3.x
- Flux Pro

## Installation

```bash
composer require arkhas/livewire-datatable
```

Publish the config file (optional):

```bash
php artisan vendor:publish --tag=livewire-datatable-config
```

Publish the views for customization (optional):

```bash
php artisan vendor:publish --tag=livewire-datatable-views
```

## Quick Start

### 1. Create a Datatable Component

```bash
php artisan make:datatable TasksTable
```

### 2. Configure Your Datatable

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Arkhas\LivewireDatatable\Traits\WithDatatable;
use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Columns\Column;
use Arkhas\LivewireDatatable\Columns\CheckboxColumn;
use Arkhas\LivewireDatatable\Columns\ActionColumn;
use Arkhas\LivewireDatatable\Filters\Filter;
use Arkhas\LivewireDatatable\Filters\FilterOption;
use Arkhas\LivewireDatatable\Actions\TableAction;
use Arkhas\LivewireDatatable\Actions\ColumnAction;
use Arkhas\LivewireDatatable\Actions\ColumnActionGroup;
use App\Models\Task;

class TasksTable extends Component
{
    use WithDatatable;

    public function setup(): void
    {
        $table = new EloquentTable(Task::with('assignee'));

        $table
            ->exportName('tasks-' . date('Y-m-d'))
            ->searchable(['title', 'description'])
            ->columns([
                CheckboxColumn::make(),

                Column::make('id')
                    ->html(fn(Task $task) => (string) $task->id),

                Column::make('title')
                    ->label('Task')
                    ->width('200px')
                    ->toggable(false)
                    ->html(fn(Task $task) => "<a href='/edit/{$task->id}'>{$task->title}</a>"),

                Column::make('status')
                    ->html(fn(Task $task) => ucfirst(str_replace('_', ' ', $task->status)))
                    ->icon(fn(Task $task) => match($task->status) {
                        'todo' => 'Circle',
                        'in_progress' => 'Timer',
                        'done' => 'CheckCircle',
                        'canceled' => 'CircleOff',
                        default => null,
                    }),

                Column::make('priority')
                    ->html(fn(Task $task) => ucfirst($task->priority))
                    ->icon(fn(Task $task) => match($task->priority) {
                        'low' => 'ArrowDown',
                        'medium' => 'ArrowRight',
                        'high' => 'ArrowUp',
                        default => null,
                    }),

                Column::make('assignee.name')
                    ->label('Assignee'),

                ActionColumn::make()
                    ->action(
                        ColumnActionGroup::make()
                            ->icon('Ellipsis')
                            ->actions([
                                ColumnAction::make('edit')
                                    ->label('Edit')
                                    ->icon('Edit')
                                    ->url(fn(Task $task) => route('tasks.edit', $task->id))
                                    ->separator(),

                                ColumnAction::make('delete')
                                    ->label('Delete')
                                    ->icon('Trash2')
                                    ->confirm(fn(Task $task) => [
                                        'title' => 'Delete Task',
                                        'message' => "Are you sure you want to delete '{$task->title}'?",
                                        'confirm' => 'Delete',
                                        'cancel' => 'Cancel',
                                    ])
                                    ->handle(function (Task $task) {
                                        $task->delete();
                                        return ['success' => true, 'message' => 'Task deleted'];
                                    }),
                            ])
                    ),
            ])
            ->filters([
                Filter::make('status')
                    ->multiple()
                    ->label('Status')
                    ->options([
                        FilterOption::make('todo')
                            ->label('To Do')
                            ->icon('Circle')
                            ->count(fn() => Task::where('status', 'todo')->count())
                            ->query(fn($query, $keyword) => $query->where('status', $keyword)),

                        FilterOption::make('in_progress')
                            ->label('In Progress')
                            ->icon('Timer')
                            ->count(fn() => Task::where('status', 'in_progress')->count())
                            ->query(fn($query, $keyword) => $query->where('status', $keyword)),

                        FilterOption::make('done')
                            ->label('Done')
                            ->icon('CheckCircle')
                            ->count(fn() => Task::where('status', 'done')->count())
                            ->query(fn($query, $keyword) => $query->where('status', $keyword)),
                    ]),

                Filter::make('priority')
                    ->label('Priority')
                    ->options([
                        FilterOption::make('low')->label('Low')->icon('ArrowDown')
                            ->query(fn($query, $keyword) => $query->where('priority', $keyword)),
                        FilterOption::make('medium')->label('Medium')->icon('ArrowRight')
                            ->query(fn($query, $keyword) => $query->where('priority', $keyword)),
                        FilterOption::make('high')->label('High')->icon('ArrowUp')
                            ->query(fn($query, $keyword) => $query->where('priority', $keyword)),
                    ]),
            ])
            ->actions([
                TableAction::make('delete')
                    ->label('Delete')
                    ->icon('Trash2', position: 'right')
                    ->props(['variant' => 'destructive', 'size' => 'sm'])
                    ->confirm(fn($ids) => [
                        'title' => 'Delete ' . count($ids) . ' Task(s)',
                        'message' => 'Are you sure you want to delete the selected tasks?',
                        'confirm' => 'Delete',
                        'cancel' => 'Cancel',
                    ])
                    ->handle(function ($ids) {
                        Task::whereIn('id', $ids)->delete();
                        return ['success' => true, 'message' => 'Tasks deleted'];
                    }),
            ]);

        $this->table($table);
    }

    public function render()
    {
        return view('livewire-datatable::datatable', [
            'table' => $this->getTable(),
            'data' => $this->getData(),
        ]);
    }
}
```

### 3. Use in Your Blade View

```blade
<livewire:tasks-table />
```

## Features

### Columns

- **Column**: Standard column with HTML rendering, icons, sorting, filtering
- **CheckboxColumn**: Row selection column
- **ActionColumn**: Actions dropdown menu per row

### Column Options

```php
Column::make('name')
    ->label('Display Label')      // Custom label
    ->width('200px')              // Column width
    ->sortable(true)              // Enable sorting
    ->sortBy('custom_column')     // Custom sort column
    ->toggable(true)              // Can be hidden/shown
    ->hidden(false)               // Initially hidden
    ->html(fn($model) => ...)     // Custom HTML rendering
    ->icon(fn($model) => ...)     // Dynamic icon
    ->filter(fn($query, $v) => ...)  // Column-level filter
    ->exportAs(fn($model) => ...) // Custom export value
```

### Filters

```php
Filter::make('status')
    ->label('Status')
    ->multiple()  // Allow multiple selections
    ->options([
        FilterOption::make('active')
            ->label('Active')
            ->icon('CheckCircle')
            ->count(fn() => Model::where('status', 'active')->count())
            ->query(fn($q, $keyword) => $q->where('status', $keyword)),
    ])
```

### Actions

#### Bulk Actions (TableAction)

```php
TableAction::make('delete')
    ->label('Delete Selected')
    ->icon('Trash2', position: 'right')
    ->props(['variant' => 'destructive', 'size' => 'sm'])
    ->confirm(fn($ids) => [...])
    ->handle(fn($ids) => [...])
```

#### Row Actions (ColumnAction)

```php
ColumnAction::make('edit')
    ->label('Edit')
    ->icon('Edit')
    ->url(fn($model) => route('edit', $model->id))
    // OR
    ->handle(fn($model) => [...])
    ->confirm(fn($model) => [...])
    ->separator()  // Add separator line after
```

### Export

```php
$table->exportName('filename')
      ->exportable(true)
      ->exportFormats(['csv', 'xlsx'])
```

> **Note**: XLSX export requires `maatwebsite/excel` package.

## Configuration

```php
// config/livewire-datatable.php
return [
    'per_page' => 10,
    'per_page_options' => [10, 25, 50, 100],
    'default_sort_direction' => 'asc',
    'search_debounce' => 300,
    'export' => [
        'default_format' => 'csv',
        'formats' => ['csv', 'xlsx'],
        'chunk_size' => 1000,
    ],
];
```

## Customizing Views

Publish the views:

```bash
php artisan vendor:publish --tag=livewire-datatable-views
```

Views will be published to `resources/views/vendor/livewire-datatable/`.

## Icons

This package uses Flux Pro icons. Make sure to install the icons you need:

```bash
php artisan flux:icon Circle Timer CheckCircle CircleOff ArrowDown ArrowRight ArrowUp Ellipsis Edit Trash2 Search Check X Settings2 Download ChevronLeft ChevronRight ChevronsLeft ChevronsRight PlusCircle
```

## License

MIT License
