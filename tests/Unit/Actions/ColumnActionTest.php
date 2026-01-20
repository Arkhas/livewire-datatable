<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Actions;

use Arkhas\LivewireDatatable\Actions\ColumnAction;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

class ColumnActionTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        parent::defineDatabaseMigrations();
    }

    /** @test */
    public function it_can_be_created_with_make(): void
    {
        $action = ColumnAction::make('edit');

        $this->assertInstanceOf(ColumnAction::class, $action);
        $this->assertEquals('edit', $action->getName());
    }

    /** @test */
    public function it_can_be_created_with_constructor(): void
    {
        $action = new ColumnAction('delete');

        $this->assertEquals('delete', $action->getName());
    }

    /** @test */
    public function it_generates_label_from_name_if_not_set(): void
    {
        $action = ColumnAction::make('edit_item');

        $this->assertEquals('Edit item', $action->getLabel());
    }

    /** @test */
    public function it_can_set_string_label(): void
    {
        $action = ColumnAction::make('edit')
            ->label('Edit Record');

        $this->assertEquals('Edit Record', $action->getLabel());
    }

    /** @test */
    public function it_can_set_closure_label(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'John',
            'email' => 'john@example.com',
        ]);

        $action = ColumnAction::make('edit')
            ->label(fn($model) => "Edit {$model->name}");

        $this->assertEquals('Edit John', $action->getLabel($model));
    }

    /** @test */
    public function it_returns_closure_label_in_to_array(): void
    {
        $action = ColumnAction::make('edit_item')
            ->label(fn($model) => "Edit {$model->name}");

        // When label is a closure, toArray() returns the name
        $array = $action->toArray();
        $this->assertEquals('edit_item', $array['label']);
    }

    /** @test */
    public function it_can_set_string_icon(): void
    {
        $action = ColumnAction::make('edit')
            ->icon('pencil');

        $this->assertEquals('pencil', $action->getIcon());
    }

    /** @test */
    public function it_can_set_closure_icon(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test',
            'email' => 'test@example.com',
            'status' => 'active',
        ]);

        $action = ColumnAction::make('toggle')
            ->icon(fn($model) => $model->status === 'active' ? 'pause' : 'play');

        $this->assertEquals('pause', $action->getIcon($model));
    }

    /** @test */
    public function it_returns_string_icon_without_model(): void
    {
        $action = ColumnAction::make('edit')
            ->icon('pencil');

        $this->assertEquals('pencil', $action->getIcon());
    }

    /** @test */
    public function it_returns_null_icon_by_default(): void
    {
        $action = ColumnAction::make('edit');

        $this->assertNull($action->getIcon());
    }

    /** @test */
    public function it_can_set_string_url(): void
    {
        $action = ColumnAction::make('view')
            ->url('/items');

        $this->assertEquals('/items', $action->getUrl());
        $this->assertTrue($action->hasUrl());
    }

    /** @test */
    public function it_can_set_closure_url(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $action = ColumnAction::make('edit')
            ->url(fn($model) => "/items/{$model->id}/edit");

        $this->assertEquals("/items/{$model->id}/edit", $action->getUrl($model));
    }

    /** @test */
    public function it_does_not_have_url_by_default(): void
    {
        $action = ColumnAction::make('edit');

        $this->assertFalse($action->hasUrl());
        $this->assertNull($action->getUrl());
    }

    /** @test */
    public function it_can_set_props(): void
    {
        $action = ColumnAction::make('edit')
            ->props(['variant' => 'ghost', 'size' => 'sm']);

        $this->assertEquals(['variant' => 'ghost', 'size' => 'sm'], $action->getProps());
    }

    /** @test */
    public function it_returns_empty_props_by_default(): void
    {
        $action = ColumnAction::make('edit');

        $this->assertEquals([], $action->getProps());
    }

    /** @test */
    public function it_can_set_separator(): void
    {
        $action = ColumnAction::make('edit')
            ->separator();

        $this->assertTrue($action->hasSeparator());
    }

    /** @test */
    public function it_does_not_have_separator_by_default(): void
    {
        $action = ColumnAction::make('edit');

        $this->assertFalse($action->hasSeparator());
    }

    /** @test */
    public function it_can_disable_separator(): void
    {
        $action = ColumnAction::make('edit')
            ->separator()
            ->separator(false);

        $this->assertFalse($action->hasSeparator());
    }

    /** @test */
    public function it_can_set_handler(): void
    {
        $action = ColumnAction::make('delete')
            ->handle(fn($model) => ['success' => true, 'message' => 'Deleted']);

        $this->assertTrue($action->hasHandler());
    }

    /** @test */
    public function it_does_not_have_handler_by_default(): void
    {
        $action = ColumnAction::make('edit');

        $this->assertFalse($action->hasHandler());
    }

    /** @test */
    public function it_can_execute_action_with_handler(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $action = ColumnAction::make('delete')
            ->handle(fn($model) => [
                'success' => true,
                'message' => "Deleted {$model->name}",
            ]);

        $result = $action->execute($model);

        $this->assertTrue($result['success']);
        $this->assertEquals('Deleted Test', $result['message']);
    }

    /** @test */
    public function it_returns_error_when_executing_without_handler(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $action = ColumnAction::make('edit');

        $result = $action->execute($model);

        $this->assertFalse($result['success']);
        $this->assertEquals('No handler defined', $result['message']);
    }

    /** @test */
    public function it_can_set_confirmation(): void
    {
        $action = ColumnAction::make('delete')
            ->confirm(fn($model) => [
                'title' => 'Delete?',
                'message' => "Are you sure you want to delete {$model->name}?",
            ]);

        $this->assertTrue($action->requiresConfirmation());
    }

    /** @test */
    public function it_does_not_require_confirmation_by_default(): void
    {
        $action = ColumnAction::make('edit');

        $this->assertFalse($action->requiresConfirmation());
    }

    /** @test */
    public function it_can_get_confirmation_data(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test Item',
            'email' => 'test@example.com',
        ]);

        $action = ColumnAction::make('delete')
            ->confirm(fn($model) => [
                'title' => 'Confirm Delete',
                'message' => "Delete {$model->name}?",
            ]);

        $confirmation = $action->getConfirmation($model);

        $this->assertEquals('Confirm Delete', $confirmation['title']);
        $this->assertEquals('Delete Test Item?', $confirmation['message']);
    }

    /** @test */
    public function it_returns_null_confirmation_when_not_set(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $action = ColumnAction::make('edit');

        $this->assertNull($action->getConfirmation($model));
    }

    /** @test */
    public function it_can_convert_to_array_for_model(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $action = ColumnAction::make('edit')
            ->label(fn($model) => "Edit {$model->name}")
            ->icon('pencil')
            ->url(fn($model) => "/items/{$model->id}/edit")
            ->props(['variant' => 'ghost'])
            ->separator()
            ->handle(fn($model) => ['success' => true])
            ->confirm(fn($model) => ['title' => 'Confirm']);

        $array = $action->toArrayForModel($model);

        $this->assertEquals('edit', $array['name']);
        $this->assertEquals('Edit Test', $array['label']);
        $this->assertEquals('pencil', $array['icon']);
        $this->assertEquals("/items/{$model->id}/edit", $array['url']);
        $this->assertEquals(['variant' => 'ghost'], $array['props']);
        $this->assertTrue($array['separator']);
        $this->assertTrue($array['hasHandler']);
        $this->assertTrue($array['requiresConfirmation']);
        $this->assertEquals('action', $array['type']);
    }

    /** @test */
    public function it_can_convert_to_array_without_model(): void
    {
        $action = ColumnAction::make('edit')
            ->label('Edit Item')
            ->icon('pencil')
            ->url('/items/edit')
            ->props(['variant' => 'ghost'])
            ->separator()
            ->handle(fn($model) => ['success' => true]);

        $array = $action->toArray();

        $this->assertEquals('edit', $array['name']);
        $this->assertEquals('Edit Item', $array['label']);
        $this->assertEquals('pencil', $array['icon']);
        $this->assertEquals('/items/edit', $array['url']);
        $this->assertEquals(['variant' => 'ghost'], $array['props']);
        $this->assertTrue($array['separator']);
        $this->assertTrue($array['hasHandler']);
        $this->assertFalse($array['requiresConfirmation']);
        $this->assertEquals('action', $array['type']);
    }


    /** @test */
    public function it_returns_null_for_closure_icon_in_to_array(): void
    {
        $action = ColumnAction::make('edit')
            ->icon(fn($model) => 'pencil');

        $array = $action->toArray();

        $this->assertNull($array['icon']);
    }

    /** @test */
    public function it_returns_null_for_closure_url_in_to_array(): void
    {
        $action = ColumnAction::make('edit')
            ->url(fn($model) => "/items/{$model->id}");

        $array = $action->toArray();

        $this->assertNull($array['url']);
    }

    /** @test */
    public function it_supports_fluent_api(): void
    {
        $action = ColumnAction::make('delete')
            ->label('Delete')
            ->icon('trash')
            ->url('/delete')
            ->props(['variant' => 'danger'])
            ->separator()
            ->handle(fn($m) => ['success' => true])
            ->confirm(fn($m) => ['title' => 'Confirm']);

        $this->assertInstanceOf(ColumnAction::class, $action);
        $this->assertEquals('Delete', $action->getLabel());
        $this->assertEquals('trash', $action->getIcon());
        $this->assertEquals('/delete', $action->getUrl());
        $this->assertEquals(['variant' => 'danger'], $action->getProps());
        $this->assertTrue($action->hasSeparator());
        $this->assertTrue($action->hasHandler());
        $this->assertTrue($action->requiresConfirmation());
    }
}
