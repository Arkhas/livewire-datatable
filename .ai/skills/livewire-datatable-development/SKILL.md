---
name: livewire-datatable-development
description: Build and work with Livewire Datatable (arkhas/livewire-datatable) features including columns, filters, actions, and export. Use when creating datatables, configuring table columns, adding filters, bulk or row actions, or when working with livewire-datatable components.
---

# Livewire Datatable Development

Ce skill couvre la création et la configuration de datatables avec le package `arkhas/livewire-datatable` (Livewire + Flux UI).

## Quand utiliser ce skill

- Création d'un nouveau composant datatable
- Configuration des colonnes (standard, checkbox, actions)
- Ajout de filtres (dropdown, date, plage de dates)
- Configuration d'actions (bulk, par ligne)
- Export CSV/XLSX

## Création d'un composant Datatable

```bash
php artisan make:datatable TasksTable
```

Structure requise : composant Livewire utilisant le trait `WithDatatable` et la méthode abstraite `setup()`.

```php
use Arkhas\LivewireDatatable\Traits\WithDatatable;
use Arkhas\LivewireDatatable\Table\EloquentTable;

class TasksTable extends Component
{
    use WithDatatable;

    public function setup(): void
    {
        $table = new EloquentTable(Task::query());
        $table->searchable(['title', 'description'])
            ->columns([...])
            ->filters([...])
            ->actions([...]);
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

## Colonnes

| Classe | Usage |
|--------|-------|
| `Column::make('field')` | Colonne standard, supporte tri, HTML, icône |
| `CheckboxColumn::make()` | Sélection de lignes pour actions bulk |
| `ActionColumn::make()` | Menu d'actions par ligne |

Options courantes pour `Column` :

```php
Column::make('name')
    ->label('Display Label')
    ->width('200px')
    ->sortable()
    ->sortBy('custom_column')  // tri sur une autre colonne
    ->toggable()
    ->hidden(false)
    ->html(fn($model) => '<a href="...">' . $model->name . '</a>')
    ->icon(fn($model) => match($model->status) { 'active' => 'circle-check', default => null })
    ->filter(fn($query, $v) => $query->where(...))
    ->exportAs(fn($model) => $model->formatted_value)
```

## Filtres

### Dropdown Filter

```php
Filter::make('status')
    ->label('Status')
    ->multiple()
    ->options([
        FilterOption::make('todo')
            ->label('To Do')
            ->icon('circle')
            ->count(fn() => Task::where('status', 'todo')->count())
            ->query(fn($query, $keyword) => $query->where('status', $keyword)),
    ])
```

### DateFilter (date unique)

```php
DateFilter::make('created_at')
    ->label('Created At')
    ->column('created_at')
    ->min('2024-01-01')
    ->withToday()
    ->clearable()
```

### RangeFilter (plage de dates)

```php
RangeFilter::make('date_range')
    ->label('Date Range')
    ->column('created_at')
    ->withPresets()
    ->presets('today yesterday thisWeek last7Days thisMonth yearToDate allTime')
    ->minRange(3)
    ->maxRange(30)
```

## Actions

### Actions bulk (TableAction)

```php
TableAction::make('delete')
    ->label('Delete Selected')
    ->icon('trash-2', position: 'right')
    ->props(['variant' => 'destructive', 'size' => 'sm'])
    ->confirm(fn($ids) => ['title' => 'Confirm', 'message' => '...', 'confirm' => 'Delete', 'cancel' => 'Cancel'])
    ->handle(function ($ids) {
        Task::whereIn('id', $ids)->delete();
        return ['success' => true, 'message' => 'Tasks deleted'];
    })
```

### Actions par ligne (ColumnAction)

```php
ColumnAction::make('edit')
    ->label('Edit')
    ->icon('pencil')
    ->url(fn(Task $task) => route('tasks.edit', $task->id))
    ->separator()

ColumnAction::make('delete')
    ->label('Delete')
    ->icon('trash-2')
    ->confirm(fn(Task $task) => [...])
    ->handle(function (Task $task) {
        $task->delete();
        return ['success' => true, 'message' => 'Deleted'];
    })
```

Actions regroupées :

```php
ActionColumn::make()
    ->action(
        ColumnActionGroup::make()
            ->icon('ellipsis-vertical')
            ->actions([...])
    )
```

## Export

```php
$table->exportName('tasks-' . date('Y-m-d'))
      ->exportable(true)
      ->exportFormats(['csv', 'xlsx'])
```

> XLSX nécessite `maatwebsite/excel`.

## Intégration dans la vue Blade

```blade
<livewire:tasks-table />
```

## Icônes (Flux / Lucide)

Toutes les icônes sont en kebab-case Lucide (`circle-check`, `trash-2`, `arrow-down`). Installer les icônes via :

```bash
php artisan livewire-datatable:install
```
Ou manuellement : `php artisan flux:icon circle timer circle-check ellipsis-vertical pencil trash-2` (noms Lucide).

## Configuration

Fichier `config/livewire-datatable.php` : `per_page`, `per_page_options`, `search_debounce`, `export.formats`, etc.

## Points importants

- Le package nécessite **Livewire 3.x/4.x** et **Livewire Flux**
- Colonnes avec relations : `Column::make('assignee.name')`
- Les callbacks `confirm` et `handle` des actions doivent retourner un tableau avec `success` et `message` pour les notifications
