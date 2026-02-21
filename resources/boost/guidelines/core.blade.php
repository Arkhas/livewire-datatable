## livewire-datatable

Build and work with Livewire Datatable (arkhas/livewire-datatable) features including columns, filters, actions, and export. Use when creating datatables, configuring table columns, adding filters, bulk or row actions, or when working with livewire-datatable components.

### Features

- Feature 1: [clear & short description].

@verbatim
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
@endverbatim
