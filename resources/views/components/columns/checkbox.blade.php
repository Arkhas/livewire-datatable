@props(['row', 'selected'])

<flux:table.cell class="!w-16 !px-0">
    <div class="flex items-center justify-center w-full">
        <flux:checkbox
            wire:model.live="selected"
            value="{{ $row->id }}"
            wire:click="toggleSelect({{ $row->id }})"
            :checked="in_array($row->id, $selected)"
        />
    </div>
</flux:table.cell>
