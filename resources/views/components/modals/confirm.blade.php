@props(['confirmData'])

<flux:modal wire:model="showConfirmModal" class="max-w-md">
    @if(!empty($confirmData))
        <div class="space-y-4">
            <flux:heading size="lg">
                {{ $confirmData['title'] ?? __('livewire-datatable::messages.confirm_action') }}
            </flux:heading>

            <flux:text>
                {{ $confirmData['message'] ?? __('livewire-datatable::messages.confirm_message') }}
            </flux:text>

            <div class="flex justify-end gap-3 pt-4">
                <flux:button variant="ghost" wire:click="cancelConfirm">
                    {{ $confirmData['cancel'] ?? __('livewire-datatable::messages.cancel') }}
                </flux:button>

                <flux:button
                    variant="{{ isset($confirmData['variant']) ? $confirmData['variant'] : 'primary' }}"
                    wire:click="confirmAction"
                >
                    {{ $confirmData['confirm'] ?? __('livewire-datatable::messages.confirm') }}
                </flux:button>
            </div>
        </div>
    @endif
</flux:modal>
