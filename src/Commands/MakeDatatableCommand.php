<?php

namespace Arkhas\LivewireDatatable\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeDatatableCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:datatable {name : The name of the datatable component}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Livewire datatable component';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Datatable';

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/datatable.stub';
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\\Livewire';
    }

    /**
     * Build the class with the given name.
     */
    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        return $this->replaceModel($stub);
    }

    /**
     * Replace the model placeholder.
     */
    protected function replaceModel(string $stub): string
    {
        $model = Str::studly(Str::beforeLast(class_basename($this->argument('name')), 'Table'));

        return str_replace('{{ model }}', $model, $stub);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        parent::handle();

        $this->info('Datatable component created successfully.');
        $this->line('');
        $this->line('Don\'t forget to:');
        $this->line('1. Update the model import in your component');
        $this->line('2. Configure your columns, filters, and actions');
        $this->line('3. Add the component to your Blade view: <livewire:' . Str::kebab(class_basename($this->argument('name'))) . ' />');
    }
}
