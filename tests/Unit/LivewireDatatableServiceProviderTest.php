<?php

test('it registers config', function () {
    expect(config('livewire-datatable'))->not->toBeNull()
        ->and(config('livewire-datatable'))->toBeArray();
});

test('it has default per page config', function () {
    expect(config('livewire-datatable.per_page'))->toBe(10);
});

test('it has default sort direction config', function () {
    expect(config('livewire-datatable.default_sort_direction'))->toBe('asc');
});

test('it has export chunk size config', function () {
    expect(config('livewire-datatable.export.chunk_size'))->toBe(1000);
});

test('it loads views', function () {
    expect(view()->exists('livewire-datatable::datatable'))->toBeTrue();
});

test('it registers make datatable command', function () {
    $commands = \Artisan::all();
    
    expect($commands)->toHaveKey('make:datatable');
});
