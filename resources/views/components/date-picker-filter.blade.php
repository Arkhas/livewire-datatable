<div class="flex items-center h-8 border border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg overflow-hidden">
    <flux:date-picker
        wire:model.live.debounce.300ms="{{ $wireModel }}"
        :mode="$dateMode"
        :value="$formattedValue"
        :with-presets="$withPresets"
        :presets="$presets"
        :min-range="$minRange"
        :max-range="$maxRange"      
        :min="$min"
        :max="$max"
        :with-today="$withToday"
        :selectable-header="$selectableHeader"
        :clearable="$clearable"
        :disabled="$disabled"
        :invalid="$invalid"
        :locale="$locale"
        :open-to="$openTo"
        :force-open-to="$forceOpenTo"
        :months="$months"
        :start-day="$startDay"
        :week-numbers="$weekNumbers"
        :with-inputs="$withInputs"
        :with-confirmation="$withConfirmation"
        :unavailable="$unavailable"
        :fixed-weeks="$fixedWeeks"
    >
        <x-slot name="trigger">
            <flux:date-picker.button
                placeholder="{{ $placeholder }}"
                size="sm"
                class="!border-0 !rounded-none"
            />
        </x-slot>
    </flux:date-picker>
</div>
