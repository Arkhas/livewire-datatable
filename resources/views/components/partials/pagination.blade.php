@props(['table', 'data'])

<div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-4 px-2">
    <div class="text-sm text-zinc-600 dark:text-zinc-400">
        <span x-show="selected.length > 0" x-cloak>
            <span x-text="selected.length"></span>
            <span
                x-text="selected.length === 1
                    ? @json(trans_choice('livewire-datatable::messages.rows_selected_of', 1, ['total' => $data->total()]))
                    : @json(trans_choice('livewire-datatable::messages.rows_selected_of', 2, ['total' => $data->total()]))"
            ></span>
        </span>
        <span x-show="selected.length === 0">
            {{ trans_choice('livewire-datatable::messages.showing_results', $data->total(), ['first' => $data->firstItem() ?? 0, 'last' => $data->lastItem() ?? 0, 'total' => $data->total()]) }}
        </span>
    </div>

    <div class="flex items-center gap-4">
        {{-- Rows per page --}}
        <div class="flex items-center gap-2">
            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ __('livewire-datatable::messages.rows_per_page') }}</span>
            <flux:select wire:model.live="perPage" size="sm" class="w-20">
                @foreach($table->getPerPageOptions() as $option)
                    <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        {{-- Page info --}}
        <span class="text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('livewire-datatable::messages.page_info', ['current' => $data->currentPage(), 'last' => $data->lastPage()]) }}
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
