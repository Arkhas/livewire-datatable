@props(['row', 'selected'])

<flux:table.cell class="w-12">
    <flux:checkbox
        wire:model.live="selected"
        value="{{ $row->id }}"
        wire:click="toggleSelect({{ $row->id }})"
        :checked="in_array($row->id, $selected)"
    />
</flux:table.cell>
