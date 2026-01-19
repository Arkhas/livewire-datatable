@props(['confirmData'])

<flux:modal wire:model="showConfirmModal" class="max-w-md">
    @if(!empty($confirmData))
        <div class="space-y-4">
            <flux:heading size="lg">
                {{ $confirmData['title'] ?? 'Confirm Action' }}
            </flux:heading>

            <flux:text>
                {{ $confirmData['message'] ?? 'Are you sure you want to perform this action?' }}
            </flux:text>

            <div class="flex justify-end gap-3 pt-4">
                <flux:button variant="ghost" wire:click="cancelConfirm">
                    {{ $confirmData['cancel'] ?? 'Cancel' }}
                </flux:button>

                <flux:button
                    variant="{{ isset($confirmData['variant']) ? $confirmData['variant'] : 'primary' }}"
                    wire:click="confirmAction"
                >
                    {{ $confirmData['confirm'] ?? 'Confirm' }}
                </flux:button>
            </div>
        </div>
    @endif
</flux:modal>
