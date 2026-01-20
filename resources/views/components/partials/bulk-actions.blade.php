@props(['table', 'data'])

<div class="flex items-center gap-2" x-show="selected.length > 0" x-cloak>
    <span class="text-sm text-zinc-600 dark:text-zinc-400">
        <span x-text="selected.length"></span> of {{ $data->total() }} row(s) selected.
    </span>

    <flux:dropdown>
        <flux:button size="sm" icon="ellipsis-vertical" variant="ghost" />

        <flux:menu>
            @foreach($table->getActions() as $action)
                @if($action->toArray()['type'] === 'group')
                    {{-- Group separator --}}
                    <flux:menu.separator />
                    <div class="px-3 py-1 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ $action->getLabel() }}
                    </div>
                    @foreach($action->getActions() as $subAction)
                        <flux:menu.item
                            @click="executeBulkAction('{{ $subAction->getName() }}')"
                            :variant="$subAction->getProps()['variant'] ?? null"
                        >
                            <div class="flex items-center gap-2">
                                @if($subAction->getIcon())
                                    <flux:icon name="{{ strtolower($subAction->getIcon()) }}" class="size-4" />
                                @endif
                                {{ $subAction->getLabel() }}
                            </div>
                        </flux:menu.item>
                    @endforeach
                @else
                    <flux:menu.item
                        @click="executeBulkAction('{{ $action->getName() }}')"
                        :variant="$action->getProps()['variant'] ?? null"
                    >
                        <div class="flex items-center gap-2">
                            @if($action->getIcon())
                                <flux:icon name="{{ strtolower($action->getIcon()) }}" class="size-4" />
                            @endif
                            {{ $action->getLabel() }}
                        </div>
                    </flux:menu.item>
                @endif
            @endforeach
        </flux:menu>
    </flux:dropdown>
</div>
