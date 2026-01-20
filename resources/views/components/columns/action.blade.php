@props(['row', 'column'])

<flux:table.cell class="w-12 py-1!">
    @php
        $action = $column->getAction();
    @endphp

    @if($action)
        @if($action->toArray()['type'] === 'group')
            <flux:dropdown position="bottom end">
                <flux:button variant="ghost" size="sm" icon="{{ strtolower($action->getIcon() ?? 'ellipsis') }}" />

                <flux:menu keep-open>
                    @foreach($action->getActions() as $subAction)
                        @php
                            $actionData = $subAction->toArrayForModel($row);
                        @endphp

                        @if($subAction->hasSeparator())
                            <flux:menu.separator />
                        @endif

                        @if($actionData['url'])
                            <flux:menu.item
                                :href="$actionData['url']"
                                :variant="$actionData['props']['variant'] ?? null"
                            >
                                <div class="flex items-center gap-2">
                                    @if($actionData['icon'])
                                        <flux:icon name="{{ strtolower($actionData['icon']) }}" class="size-4" />
                                    @endif
                                    {{ $actionData['label'] }}
                                </div>
                            </flux:menu.item>
                        @else
                            <flux:menu.item
                                wire:click="executeRowAction('{{ $subAction->getName() }}', {{ $row->id }})"
                                :variant="$actionData['props']['variant'] ?? null"
                            >
                                <div class="flex items-center gap-2">
                                    @if($actionData['icon'])
                                        <flux:icon name="{{ strtolower($actionData['icon']) }}" class="size-4" />
                                    @endif
                                    {{ $actionData['label'] }}
                                </div>
                            </flux:menu.item>
                        @endif
                    @endforeach
                </flux:menu>
            </flux:dropdown>
        @else
            @php
                $actionData = $action->toArrayForModel($row);
            @endphp

            @if($actionData['url'])
                <flux:button
                    variant="ghost"
                    size="sm"
                    :href="$actionData['url']"
                    :icon="$actionData['icon'] ? strtolower($actionData['icon']) : null"
                >
                    @if($actionData['label'])
                        {{ $actionData['label'] }}
                    @endif
                </flux:button>
            @else
                <flux:button
                    variant="ghost"
                    size="sm"
                    wire:click="executeRowAction('{{ $action->getName() }}', {{ $row->id }})"
                    :icon="$actionData['icon'] ? strtolower($actionData['icon']) : null"
                >
                    @if($actionData['label'])
                        {{ $actionData['label'] }}
                    @endif
                </flux:button>
            @endif
        @endif
    @endif
</flux:table.cell>
