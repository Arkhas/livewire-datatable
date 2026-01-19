@props(['table', 'filters', 'selected', 'data'])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex flex-1 items-center gap-2">
        {{-- Search --}}
        @if($table->isSearchable())
            <div class="w-full sm:w-64">
                <flux:input
                    type="search"
                    wire:model.live.debounce.{{ config('livewire-datatable.search_debounce', 300) }}ms="search"
                    placeholder="{{ $table->getSearchPlaceholder() }}"
                    icon="search"
                />
            </div>
        @endif

        {{-- Filters --}}
        <x-livewire-datatable::partials.filters :table="$table" :filters="$filters" />

        {{-- Reset Filters --}}
        @if(!empty($filters))
            <flux:button variant="ghost" size="sm" wire:click="resetFilters">
                Reset
            </flux:button>
            <flux:button variant="ghost" size="sm" icon="x" wire:click="resetFilters" />
        @endif
    </div>

    <div class="flex items-center gap-2">
        {{-- Bulk Actions --}}
        @if(!empty($selected))
            <x-livewire-datatable::partials.bulk-actions :table="$table" :selected="$selected" :data="$data" />
        @endif

        {{-- Export --}}
        @if($table->isExportable())
            <flux:dropdown>
                <flux:button variant="ghost" size="sm" icon="download">
                    Export
                </flux:button>

                <flux:menu>
                    @foreach($table->getExportFormats() as $format)
                        <flux:menu.item wire:click="export('{{ $format }}')">
                            {{ strtoupper($format) }}
                        </flux:menu.item>
                    @endforeach
                </flux:menu>
            </flux:dropdown>
        @endif

        {{-- View Options (Column Toggle) --}}
        @if(count($table->getToggableColumns()) > 0)
            <flux:dropdown>
                <flux:button variant="ghost" size="sm" icon-trailing="settings-2">
                    View
                </flux:button>

                <flux:menu>
                    @foreach($table->getToggableColumns() as $column)
                        <flux:menu.item wire:click="toggleColumn('{{ $column->getName() }}')">
                            <div class="flex items-center gap-2">
                                @if($this->isColumnVisible($column->getName()))
                                    <flux:icon name="check" class="size-4" />
                                @else
                                    <span class="size-4"></span>
                                @endif
                                {{ $column->getLabel() }}
                            </div>
                        </flux:menu.item>
                    @endforeach
                </flux:menu>
            </flux:dropdown>
        @endif
    </div>
</div>
