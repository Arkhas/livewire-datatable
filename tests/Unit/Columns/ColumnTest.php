<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Columns;

use Arkhas\LivewireDatatable\Columns\Column;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestRelatedModel;
use Illuminate\Database\Eloquent\Builder;

class ColumnTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        parent::defineDatabaseMigrations();
    }

    /** @test */
    public function it_can_be_created_with_make(): void
    {
        $column = Column::make('name');

        $this->assertInstanceOf(Column::class, $column);
        $this->assertEquals('name', $column->getName());
    }

    /** @test */
    public function it_can_be_created_with_constructor(): void
    {
        $column = new Column('email');

        $this->assertEquals('email', $column->getName());
    }

    /** @test */
    public function it_generates_label_from_name_if_not_set(): void
    {
        $column = Column::make('first_name');

        $this->assertEquals('First name', $column->getLabel());
    }

    /** @test */
    public function it_handles_dot_notation_in_label_generation(): void
    {
        $column = Column::make('user.name');

        $this->assertEquals('User name', $column->getLabel());
    }

    /** @test */
    public function it_can_set_and_get_label(): void
    {
        $column = Column::make('name')
            ->label('Full Name');

        $this->assertEquals('Full Name', $column->getLabel());
    }

    /** @test */
    public function it_can_set_and_get_width(): void
    {
        $column = Column::make('name')
            ->width('200px');

        $this->assertEquals('200px', $column->getWidth());
    }

    /** @test */
    public function it_returns_null_width_by_default(): void
    {
        $column = Column::make('name');

        $this->assertNull($column->getWidth());
    }

    /** @test */
    public function it_is_sortable_by_default(): void
    {
        $column = Column::make('name');

        $this->assertTrue($column->isSortable());
    }

    /** @test */
    public function it_can_disable_sorting(): void
    {
        $column = Column::make('name')
            ->sortable(false);

        $this->assertFalse($column->isSortable());
    }

    /** @test */
    public function it_can_enable_sorting(): void
    {
        $column = Column::make('name')
            ->sortable(false)
            ->sortable(true);

        $this->assertTrue($column->isSortable());
    }

    /** @test */
    public function it_uses_name_as_default_sort_column(): void
    {
        $column = Column::make('email');

        $this->assertEquals('email', $column->getSortColumn());
    }

    /** @test */
    public function it_can_set_custom_sort_column(): void
    {
        $column = Column::make('name')
            ->sortBy('users.name');

        $this->assertEquals('users.name', $column->getSortColumn());
    }

    /** @test */
    public function it_is_toggable_by_default(): void
    {
        $column = Column::make('name');

        $this->assertTrue($column->isToggable());
    }

    /** @test */
    public function it_can_disable_toggable(): void
    {
        $column = Column::make('name')
            ->toggable(false);

        $this->assertFalse($column->isToggable());
    }

    /** @test */
    public function it_is_not_hidden_by_default(): void
    {
        $column = Column::make('name');

        $this->assertFalse($column->isHidden());
    }

    /** @test */
    public function it_can_be_hidden(): void
    {
        $column = Column::make('name')
            ->hidden();

        $this->assertTrue($column->isHidden());
    }

    /** @test */
    public function it_can_be_shown_after_hidden(): void
    {
        $column = Column::make('name')
            ->hidden()
            ->hidden(false);

        $this->assertFalse($column->isHidden());
    }

    /** @test */
    public function it_can_get_html_content_from_model(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $column = Column::make('name');

        $this->assertEquals('John Doe', $column->getHtml($model));
    }

    /** @test */
    public function it_escapes_html_content(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => '<script>alert("xss")</script>',
            'email' => 'test@example.com',
        ]);

        $column = Column::make('name');

        $this->assertEquals('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $column->getHtml($model));
    }

    /** @test */
    public function it_can_use_custom_html_callback(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'John',
            'email' => 'john@example.com',
        ]);

        $column = Column::make('name')
            ->html(fn($model) => '<strong>' . $model->name . '</strong>');

        $this->assertEquals('<strong>John</strong>', $column->getHtml($model));
    }

    /** @test */
    public function it_can_get_value_from_model(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $column = Column::make('email');

        $this->assertEquals('jane@example.com', $column->getValue($model));
    }

    /** @test */
    public function it_can_get_value_with_dot_notation_for_single_relation(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $related = TestRelatedModel::create([
            'test_model_id' => $model->id,
            'title' => 'Related Title',
        ]);

        // Use belongsTo relation on the related model instead
        $column = Column::make('testModel.name');

        $this->assertEquals('Test User', $column->getValue($related));
    }

    /** @test */
    public function it_returns_null_for_missing_relation(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $column = Column::make('missingRelation.field');

        $this->assertNull($column->getValue($model));
    }

    /** @test */
    public function it_returns_empty_string_for_null_value(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test User',
            'email' => null,
        ]);

        $column = Column::make('email');

        $this->assertEquals('', $column->getHtml($model));
    }

    /** @test */
    public function it_does_not_have_icon_by_default(): void
    {
        $column = Column::make('name');

        $this->assertFalse($column->hasIcon());
    }

    /** @test */
    public function it_can_set_icon_callback(): void
    {
        $column = Column::make('status')
            ->icon(fn($model) => $model->status === 'active' ? 'check' : 'x');

        $this->assertTrue($column->hasIcon());
    }

    /** @test */
    public function it_can_get_icon_from_model(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test',
            'email' => 'test@example.com',
            'status' => 'active',
        ]);

        $column = Column::make('status')
            ->icon(fn($model) => $model->status === 'active' ? 'check-circle' : 'x-circle');

        $this->assertEquals('check-circle', $column->getIcon($model));
    }

    /** @test */
    public function it_returns_null_icon_when_no_callback(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $column = Column::make('name');

        $this->assertNull($column->getIcon($model));
    }

    /** @test */
    public function it_does_not_have_filter_by_default(): void
    {
        $column = Column::make('name');

        $this->assertFalse($column->hasFilter());
    }

    /** @test */
    public function it_can_set_filter_callback(): void
    {
        $column = Column::make('status')
            ->filter(fn($query, $value) => $query->where('status', $value));

        $this->assertTrue($column->hasFilter());
    }

    /** @test */
    public function it_can_apply_filter_to_query(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'Active User', 'email' => 'active@example.com', 'status' => 'active']);
        TestModel::create(['name' => 'Inactive User', 'email' => 'inactive@example.com', 'status' => 'inactive']);

        $column = Column::make('status')
            ->filter(fn($query, $value) => $query->where('status', $value));

        $query = TestModel::query();
        $column->applyFilter($query, 'active');

        $this->assertEquals(1, $query->count());
        $this->assertEquals('Active User', $query->first()->name);
    }

    /** @test */
    public function it_does_nothing_when_applying_filter_without_callback(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
        TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

        $column = Column::make('name');

        $query = TestModel::query();
        $column->applyFilter($query, 'some value');

        $this->assertEquals(2, $query->count());
    }

    /** @test */
    public function it_can_set_export_callback(): void
    {
        $column = Column::make('status')
            ->exportAs(fn($model) => strtoupper($model->status));

        $this->assertInstanceOf(Column::class, $column);
    }

    /** @test */
    public function it_can_get_export_value_with_callback(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test',
            'email' => 'test@example.com',
            'status' => 'active',
        ]);

        $column = Column::make('status')
            ->exportAs(fn($model) => strtoupper($model->status));

        $this->assertEquals('ACTIVE', $column->getExportValue($model));
    }

    /** @test */
    public function it_uses_value_as_export_value_without_callback(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create([
            'name' => 'Test',
            'email' => 'test@example.com',
            'status' => 'active',
        ]);

        $column = Column::make('status');

        $this->assertEquals('active', $column->getExportValue($model));
    }

    /** @test */
    public function it_can_be_converted_to_array(): void
    {
        $column = Column::make('email')
            ->label('Email Address')
            ->width('150px')
            ->sortable(true)
            ->toggable(false)
            ->hidden(true)
            ->icon(fn($model) => 'mail');

        $array = $column->toArray();

        $this->assertEquals([
            'name' => 'email',
            'label' => 'Email Address',
            'width' => '150px',
            'sortable' => true,
            'toggable' => false,
            'hidden' => true,
            'hasIcon' => true,
            'hasFilter' => false,
            'type' => 'column',
        ], $array);
    }

    /** @test */
    public function it_supports_fluent_api(): void
    {
        $column = Column::make('name')
            ->label('Name')
            ->width('100px')
            ->sortable()
            ->toggable()
            ->hidden()
            ->sortBy('full_name')
            ->icon(fn($m) => 'icon')
            ->filter(fn($q, $v) => $q)
            ->exportAs(fn($m) => $m->name)
            ->html(fn($m) => $m->name);

        $this->assertInstanceOf(Column::class, $column);
        $this->assertEquals('Name', $column->getLabel());
        $this->assertEquals('100px', $column->getWidth());
        $this->assertTrue($column->isSortable());
        $this->assertTrue($column->isToggable());
        $this->assertTrue($column->isHidden());
        $this->assertEquals('full_name', $column->getSortColumn());
        $this->assertTrue($column->hasIcon());
        $this->assertTrue($column->hasFilter());
    }
}
