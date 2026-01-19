@props(['table', 'filters'])

@foreach($table->getFilters() as $filter)
    <flux:dropdown>
        <flux:button variant="ghost" size="sm" icon="plus-circle">
            {{ $filter->getLabel() }}
            @if(isset($filters[$filter->getName()]) && count($filters[$filter->getName()]) > 0)
                <flux:badge size="sm" class="ml-1">
                    {{ count($filters[$filter->getName()]) }} selected
                </flux:badge>
            @endif
        </flux:button>

        <flux:menu class="min-w-48">
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
@endforeach
