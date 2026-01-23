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
                {{ __('livewire-datatable::messages.reset') }}
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
                    {{ __('livewire-datatable::messages.export') }}
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
                    {{ __('livewire-datatable::messages.view') }}
                </flux:button>

                <flux:menu keep-open x-data="{ columnSearch: '' }">
                    {{-- Column Search --}}
                    <div class="px-2 pb-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
                                <flux:icon name="magnifying-glass" class="size-4 text-zinc-400 dark:text-zinc-500" />
                            </div>
                            <input
                                type="text"
                                x-model="columnSearch"
                                placeholder="{{ __('livewire-datatable::messages.search_columns') }}"
                                class="w-full pl-6 pr-2 py-1.5 text-sm border-0 rounded-md bg-transparent text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0"
                                @click.stop
                                x-on:keydown.capture.stop
                            />
                        </div>
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
