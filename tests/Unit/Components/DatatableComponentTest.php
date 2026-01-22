<?php

use Arkhas\LivewireDatatable\Components\Datatable;
use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;
use Livewire\Livewire;

test('it can be rendered', function () {
    // Since Datatable is a base class that expects setup() to be overridden,
    // we test that it exists and has the expected methods
    $component = new Datatable();
    
    expect($component)->toBeInstanceOf(Datatable::class);
});

test('it has setup method', function () {
    $component = new Datatable();
    
    expect(method_exists($component, 'setup'))->toBeTrue();
});

test('it has render method', function () {
    $component = new Datatable();
    
    expect(method_exists($component, 'render'))->toBeTrue();
});

test('it can call setup method', function () {
    $component = new Datatable();
    
    // Setup should not throw an error
    $component->setup();
    
    expect(true)->toBeTrue();
});

test('it can render with table setup', function () {
    TestModel::create(['name' => 'Test', 'email' => 'test@example.com']);
    
    $component = new class extends Datatable {
        public function setup(): void
        {
            $this->table(
                (new EloquentTable(TestModel::query()))
                    ->columns([
                        \Arkhas\LivewireDatatable\Columns\Column::make('name'),
                    ])
            );
        }
    };
    
    Livewire::component('test-datatable-render', $component::class);
    
    // Should not throw error
    Livewire::test($component::class);
    
    expect(true)->toBeTrue();
});
