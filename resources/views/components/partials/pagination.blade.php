@props(['table', 'data'])

<div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-4 px-2">
    <div class="text-sm text-zinc-600 dark:text-zinc-400">
        <span x-show="selected.length > 0" x-cloak>
            <span x-text="selected.length"></span> of {{ $data->total() }} row(s) selected.
        </span>
        <span x-show="selected.length === 0">
            Showing {{ $data->firstItem() ?? 0 }} to {{ $data->lastItem() ?? 0 }} of {{ $data->total() }} results.
        </span>
    </div>

    <div class="flex items-center gap-4">
        {{-- Rows per page --}}
        <div class="flex items-center gap-2">
            <span class="text-sm text-zinc-600 dark:text-zinc-400">Rows per page</span>
            <flux:select wire:model.live="perPage" size="sm" class="w-20">
                @foreach($table->getPerPageOptions() as $option)
                    <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        {{-- Page info --}}
        <span class="text-sm text-zinc-600 dark:text-zinc-400">
            Page {{ $data->currentPage() }} of {{ $data->lastPage() }}
        </span>

        {{-- Navigation --}}
        <div class="flex items-center gap-1">
            <flux:button
                variant="ghost"
                size="sm"
                icon="chevrons-left"
                wire:click="gotoPage(1)"
                :disabled="$data->onFirstPage()"
            />
            <flux:button
                variant="ghost"
                size="sm"
                icon="chevron-left"
                wire:click="previousPage"
                :disabled="$data->onFirstPage()"
            />
            <flux:button
                variant="ghost"
                size="sm"
                icon="chevron-right"
                wire:click="nextPage"
                :disabled="!$data->hasMorePages()"
            />
            <flux:button
                variant="ghost"
                size="sm"
                icon="chevrons-right"
                wire:click="gotoPage({{ $data->lastPage() }})"
                :disabled="!$data->hasMorePages()"
            />
        </div>
    </div>
</div>
