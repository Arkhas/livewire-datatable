@props(['row', 'column'])

<flux:table.cell @if($column->getWidth()) style="width: {{ $column->getWidth() }}" @endif>
    <div class="flex items-center gap-2">
        @if($column->hasIcon() && $column->getIcon($row))
            <flux:icon name="{{ strtolower($column->getIcon($row)) }}" class="size-4 text-zinc-500" />
        @endif

        <span>{!! $column->getHtml($row) !!}</span>
    </div>
</flux:table.cell>
