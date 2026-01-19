<?php

namespace Arkhas\LivewireDatatable;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Arkhas\LivewireDatatable\Components\Datatable;
use Arkhas\LivewireDatatable\Commands\MakeDatatableCommand;

class LivewireDatatableServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/livewire-datatable.php',
            'livewire-datatable'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'livewire-datatable');

        // Register Blade components
        Blade::anonymousComponentPath(__DIR__ . '/../resources/views/components', 'livewire-datatable');

        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__ . '/../config/livewire-datatable.php' => config_path('livewire-datatable.php'),
            ], 'livewire-datatable-config');

            // Publish views
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/livewire-datatable'),
            ], 'livewire-datatable-views');

            // Publish stubs
            $this->publishes([
                __DIR__ . '/../stubs' => base_path('stubs/livewire-datatable'),
            ], 'livewire-datatable-stubs');

            // Register commands
            $this->commands([
                MakeDatatableCommand::class,
            ]);
        }

        // Register Livewire component
        Livewire::component('datatable', Datatable::class);
    }
}
