@props(['table', 'selected', 'data'])

<div class="flex items-center gap-2">
    <span class="text-sm text-zinc-600 dark:text-zinc-400">
        {{ count($selected) }} of {{ $data->total() }} row(s) selected.
    </span>

    @foreach($table->getActions() as $action)
        @if($action->toArray()['type'] === 'group')
            <flux:dropdown>
                <flux:button
                    :variant="$action->getProps()['variant'] ?? 'outline'"
                    :size="$action->getProps()['size'] ?? 'sm'"
                    :icon="$action->getIcon() ? strtolower($action->getIcon()) : null"
                >
                    {{ $action->getLabel() }}
                </flux:button>

                <flux:menu keep-open>
                    @foreach($action->getActions() as $subAction)
                        <flux:menu.item wire:click="executeBulkAction('{{ $subAction->getName() }}')">
                            <div class="flex items-center gap-2">
                                @if($subAction->getIcon())
                                    <flux:icon name="{{ strtolower($subAction->getIcon()) }}" class="size-4" />
                                @endif
                                {{ $subAction->getLabel() }}
                            </div>
                        </flux:menu.item>
                    @endforeach
                </flux:menu>
            </flux:dropdown>
        @else
            <flux:button
                :variant="$action->getProps()['variant'] ?? 'outline'"
                :size="$action->getProps()['size'] ?? 'sm'"
                wire:click="executeBulkAction('{{ $action->getName() }}')"
                :icon="$action->getIcon() && $action->getIconPosition() === 'left' ? strtolower($action->getIcon()) : null"
                :icon-trailing="$action->getIcon() && $action->getIconPosition() === 'right' ? strtolower($action->getIcon()) : null"
            >
                {{ $action->getLabel() }}
            </flux:button>
        @endif
    @endforeach
</div>
