@props(['row'])

<flux:table.cell class="!w-16 !px-0">
    <div class="flex items-center justify-center w-full">
        <flux:checkbox
            value="{{ $row->id }}"
            x-bind:checked="isSelected({{ $row->id }})"
            @change="toggleSelection({{ $row->id }})"
        />
    </div>
</flux:table.cell>
