@props(['table', 'filters', 'data'])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex flex-1 items-center gap-2">
        {{-- Search --}}
        @if($table->isSearchable())
            <div class="w-[150px] lg:w-[250px]">
                <flux:input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ $table->getSearchPlaceholder() }}"
                    size="sm"
                />
            </div>
        @endif

        {{-- Filters --}}
        @if(count($table->getFilters()) > 0)
            <x-livewire-datatable::partials.filters :table="$table" :filters="$filters" />
        @endif

        {{-- Reset Filters --}}
        @if(!empty($filters))
            <flux:button variant="ghost" size="sm" icon-trailing="x-mark" wire:click="resetFilters">
                Reset
            </flux:button>
        @endif
    </div>

    <div class="flex items-center gap-2">
        {{-- Bulk Actions --}}
        @if(count($table->getActions()) > 0)
            <x-livewire-datatable::partials.bulk-actions :table="$table" :data="$data" />
        @endif

        {{-- Export --}}
        @if($table->isExportable())
            <flux:dropdown>
                <flux:button  size="sm" icon="arrow-down-tray">
                    Export
                </flux:button>

                <flux:menu keep-open>
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
                <flux:button  size="sm" icon-trailing="adjustments-horizontal">
                    View
                </flux:button>

                <flux:menu keep-open x-data="{ columnSearch: '' }">
                    {{-- Column Search --}}
                    <div class="px-2 pb-2">
                        <input
                            type="text"
                            x-model="columnSearch"
                            placeholder="Search columns..."
                            class="w-full px-2 py-1 text-sm border border-zinc-300 dark:border-zinc-600 rounded-md bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            @click.stop
                            x-on:keydown.capture.stop
                        />
                    </div>

                    @foreach($table->getToggableColumns() as $column)
                        <flux:menu.item
                            wire:click="toggleColumn('{{ $column->getName() }}')"
                            x-show="columnSearch === '' || '{{ strtolower($column->getLabel()) }}'.includes(columnSearch.toLowerCase())"
                            x-cloak
                        >
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
