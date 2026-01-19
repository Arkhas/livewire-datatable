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
                    @if($action->getIcon()) icon="{{ strtolower($action->getIcon()) }}" @endif
                >
                    {{ $action->getLabel() }}
                </flux:button>

                <flux:menu>
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
                @if($action->getIcon() && $action->getIconPosition() === 'left') icon="{{ strtolower($action->getIcon()) }}" @endif
                @if($action->getIcon() && $action->getIconPosition() === 'right') icon-trailing="{{ strtolower($action->getIcon()) }}" @endif
            >
                {{ $action->getLabel() }}
            </flux:button>
        @endif
    @endforeach
</div>
