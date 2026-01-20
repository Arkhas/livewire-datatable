<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Commands;

use Arkhas\LivewireDatatable\Tests\TestCase;
use Illuminate\Support\Facades\File;

class MakeDatatableCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        // Clean up generated files
        $path = app_path('Livewire');
        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
        }

        parent::tearDown();
    }

    /** @test */
    public function it_can_create_datatable_component(): void
    {
        $this->artisan('make:datatable', ['name' => 'UserTable'])
            ->assertSuccessful();

        $this->assertFileExists(app_path('Livewire/UserTable.php'));
    }

    /** @test */
    public function it_uses_model_name_from_component_name(): void
    {
        $this->artisan('make:datatable', ['name' => 'ProductTable'])
            ->assertSuccessful();

        $content = File::get(app_path('Livewire/ProductTable.php'));

        $this->assertStringContainsString('Product::query()', $content);
    }

    /** @test */
    public function it_creates_file_in_correct_namespace(): void
    {
        $this->artisan('make:datatable', ['name' => 'OrderTable'])
            ->assertSuccessful();

        $content = File::get(app_path('Livewire/OrderTable.php'));

        $this->assertStringContainsString('namespace App\Livewire;', $content);
    }

    /** @test */
    public function it_outputs_helpful_messages(): void
    {
        $this->artisan('make:datatable', ['name' => 'TestTable'])
            ->expectsOutput('Datatable component created successfully.')
            ->assertSuccessful();
    }

    /** @test */
    public function it_uses_datatable_stub(): void
    {
        // Verify the stub exists
        $stubPath = __DIR__ . '/../../../stubs/datatable.stub';
        
        $this->assertFileExists($stubPath);
    }
}
