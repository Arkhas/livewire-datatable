<div
    x-data="{
        selected: [],
        pageIds: @js($data->pluck('id')->toArray()),
        get selectAll() {
            return this.pageIds.length > 0 && this.pageIds.every(id => this.selected.includes(id))
        },
        toggleSelection(id) {
            const index = this.selected.indexOf(id)
            if (index > -1) {
                this.selected.splice(index, 1)
            } else {
                this.selected.push(id)
            }
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selected = this.selected.filter(id => !this.pageIds.includes(id))
            } else {
                this.pageIds.forEach(id => {
                    if (!this.selected.includes(id)) {
                        this.selected.push(id)
                    }
                })
            }
        },
        isSelected(id) {
            return this.selected.includes(id)
        },
        executeBulkAction(name) {
            $wire.executeBulkActionWithIds(name, this.selected)
        },
        clearSelection() {
            this.selected = []
        }
    }"
    x-on:action-executed.window="clearSelection()"
    class="livewire-datatable"
>
    {{-- Toolbar: Search, Filters, Actions --}}
    <x-livewire-datatable::partials.toolbar :table="$table" :filters="$filters" :data="$data" />

    {{-- Table --}}
    <div class="mt-4 border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden">
        <flux:table>
            <flux:table.columns>
                @foreach($table->getColumns() as $column)
                    @if($this->isColumnVisible($column->getName()) && !$column->isHidden())
                        @if($column->toArray()['type'] === 'checkbox')
                            <flux:table.column class="!w-16 !px-0">
                                <div class="flex items-center justify-center w-full">
                                    <flux:checkbox
                                        x-bind:checked="selectAll"
                                        @change="toggleSelectAll()"
                                    />
                                </div>
                            </flux:table.column>
                        @elseif($column->toArray()['type'] === 'action')
                            <flux:table.column class="w-12"></flux:table.column>
                        @else
                            <flux:table.column
                                :style="$column->getWidth() ? 'width: ' . $column->getWidth() : null"
                            >
                                @if($column->isSortable())
                                    <button
                                        type="button"
                                        wire:click="sortBy('{{ $column->getName() }}')"
                                        class="flex items-center gap-1 hover:text-zinc-900 dark:hover:text-white transition-colors"
                                    >
                                        {{ $column->getLabel() }}
                                        @if($sortColumn === $column->getName())
                                            @if($sortDirection === 'asc')
                                                <flux:icon.arrow-up class="size-4" />
                                            @else
                                                <flux:icon.arrow-down class="size-4" />
                                            @endif
                                        @else
                                            <flux:icon.chevrons-up-down class="size-4 text-zinc-400" />
                                        @endif
                                    </button>
                                @else
                                    {{ $column->getLabel() }}
                                @endif
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
                                    <x-livewire-datatable::columns.checkbox :row="$row" />
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
    <x-livewire-datatable::partials.pagination :table="$table" :data="$data" />

    {{-- Confirmation Modal --}}
    <x-livewire-datatable::modals.confirm :confirmData="$confirmData" />
</div>
