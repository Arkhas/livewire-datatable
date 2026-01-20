<div class="livewire-datatable">
    {{-- Toolbar: Search, Filters, Actions --}}
    <x-livewire-datatable::partials.toolbar :table="$table" :filters="$filters" :selected="$selected" :data="$data" />

    {{-- Table --}}
    <div class="mt-4 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden">
        <flux:table>
            <flux:table.columns>
                @foreach($table->getColumns() as $column)
                    @if($this->isColumnVisible($column->getName()) && !$column->isHidden())
                        @if($column->toArray()['type'] === 'checkbox')
                            <flux:table.column class="w-12">
                                <flux:checkbox
                                    wire:model.live="selectAll"
                                    wire:click="toggleSelectAll"
                                />
                            </flux:table.column>
                        @elseif($column->toArray()['type'] === 'action')
                            <flux:table.column class="w-12"></flux:table.column>
                        @else
                            <flux:table.column
                                :sortable="$column->isSortable()"
                                :sorted="$sortColumn === $column->getName()"
                                :direction="$sortColumn === $column->getName() ? $sortDirection : null"
                                wire:click="{{ $column->isSortable() ? 'sortBy(\'' . $column->getName() . '\')' : '' }}"
                                :style="$column->getWidth() ? 'width: ' . $column->getWidth() : null"
                            >
                                {{ $column->getLabel() }}
                            </flux:table.column>
                        @endif
                    @endif
                @endforeach
            </flux:table.columns>

            <flux:table.rows>
                @forelse($data as $row)
                    <flux:table.row :key="$row->id" wire:key="row-{{ $row->id }}">
                        @foreach($table->getColumns() as $column)
                            @if($this->isColumnVisible($column->getName()) && !$column->isHidden())
                                @if($column->toArray()['type'] === 'checkbox')
                                    <x-livewire-datatable::columns.checkbox :row="$row" :selected="$selected" />
                                @elseif($column->toArray()['type'] === 'action')
                                    <x-livewire-datatable::columns.action :row="$row" :column="$column" />
                                @else
                                    <x-livewire-datatable::columns.default :row="$row" :column="$column" />
                                @endif
                            @endif
                        @endforeach
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell :colspan="count($table->getVisibleColumns())">
                            <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                                No results found.
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    {{-- Pagination --}}
    <x-livewire-datatable::partials.pagination :table="$table" :data="$data" :selected="$selected" />

    {{-- Confirmation Modal --}}
    <x-livewire-datatable::modals.confirm :confirmData="$confirmData" />
</div>
