@props(['row', 'column'])

<flux:table.cell class="w-12">
    @php
        $action = $column->getAction();
    @endphp

    @if($action)
        @if($action->toArray()['type'] === 'group')
            <flux:dropdown position="bottom end">
                <flux:button variant="ghost" size="sm" icon="{{ strtolower($action->getIcon() ?? 'ellipsis') }}" />

                <flux:menu>
                    @foreach($action->getActions() as $subAction)
                        @php
                            $actionData = $subAction->toArrayForModel($row);
                        @endphp

                        @if($subAction->hasSeparator())
                            <flux:menu.separator />
                        @endif

                        @if($actionData['url'])
                            <flux:menu.item :href="$actionData['url']">
                                <div class="flex items-center gap-2">
                                    @if($actionData['icon'])
                                        <flux:icon name="{{ strtolower($actionData['icon']) }}" class="size-4" />
                                    @endif
                                    {{ $actionData['label'] }}
                                </div>
                            </flux:menu.item>
                        @else
                            <flux:menu.item wire:click="executeRowAction('{{ $subAction->getName() }}', {{ $row->id }})">
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
                    @if($actionData['icon']) icon="{{ strtolower($actionData['icon']) }}" @endif
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
                    @if($actionData['icon']) icon="{{ strtolower($actionData['icon']) }}" @endif
                >
                    @if($actionData['label'])
                        {{ $actionData['label'] }}
                    @endif
                </flux:button>
            @endif
        @endif
    @endif
</flux:table.cell>
