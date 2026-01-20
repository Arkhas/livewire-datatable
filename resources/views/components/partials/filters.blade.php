@props(['table', 'filters'])

@foreach($table->getFilters() as $filter)
    @php
        $selectedCount = isset($filters[$filter->getName()]) ? count($filters[$filter->getName()]) : 0;
        $hasSelection = $selectedCount > 0;
    @endphp

    <div class="flex items-center h-8 border border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg overflow-hidden">
        <flux:dropdown>
            <flux:button variant="ghost" size="sm" icon="circle-plus" class="!border-0 !rounded-none">
                {{ $filter->getLabel() }}
            </flux:button>

            <flux:menu keep-open class="min-w-48">
                @foreach($filter->getOptions() as $option)
                    <flux:menu.item
                        wire:click="toggleFilter('{{ $filter->getName() }}', '{{ $option->getName() }}')"
                    >
                        <div class="flex items-center justify-between w-full gap-3">
                            <div class="flex items-center gap-2">
                                @if(isset($filters[$filter->getName()]) && in_array($option->getName(), $filters[$filter->getName()]))
                                    <flux:icon name="check" class="size-4" />
                                @else
                                    <span class="size-4"></span>
                                @endif

                                @if($option->getIcon())
                                    <flux:icon name="{{ strtolower($option->getIcon()) }}" class="size-4 text-zinc-500" />
                                @endif

                                <span>{{ $option->getLabel() }}</span>
                            </div>

                            @if($option->getCount() !== null)
                                <span class="text-xs text-zinc-500">{{ $option->getCount() }}</span>
                            @endif
                        </div>
                    </flux:menu.item>
                @endforeach
            </flux:menu>
        </flux:dropdown>

        @if($hasSelection)
            <span class="inline-flex items-center h-full px-2.5 text-sm bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                {{ $selectedCount }} selected
            </span>
        @endif
    </div>
@endforeach
