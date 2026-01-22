<?php

use Illuminate\Support\Facades\File;

afterEach(function () {
    // Clean up generated files
    $path = app_path('Livewire');
    if (File::isDirectory($path)) {
        File::deleteDirectory($path);
    }
});

test('it can create datatable component', function () {
    $this->artisan('make:datatable', ['name' => 'UserTable'])
        ->assertSuccessful();

    expect(app_path('Livewire/UserTable.php'))->toBeFile();
});

test('it uses model name from component name', function () {
    $this->artisan('make:datatable', ['name' => 'ProductTable'])
        ->assertSuccessful();

    $content = File::get(app_path('Livewire/ProductTable.php'));

    expect($content)->toContain('Product::query()');
});

test('it creates file in correct namespace', function () {
    $this->artisan('make:datatable', ['name' => 'OrderTable'])
        ->assertSuccessful();

    $content = File::get(app_path('Livewire/OrderTable.php'));

    expect($content)->toContain('namespace App\Livewire;');
});

test('it outputs helpful messages', function () {
    $this->artisan('make:datatable', ['name' => 'TestTable'])
        ->expectsOutput('Datatable component created successfully.')
        ->assertSuccessful();
});

test('it uses datatable stub', function () {
    // Verify the stub exists
    $stubPath = __DIR__ . '/../../../stubs/datatable.stub';
    
    expect($stubPath)->toBeFile();
});
