<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Filters;

use Arkhas\LivewireDatatable\Filters\FilterOption;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

class FilterOptionTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        parent::defineDatabaseMigrations();
    }

    /** @test */
    public function it_can_be_created_with_make(): void
    {
        $option = FilterOption::make('active');

        $this->assertInstanceOf(FilterOption::class, $option);
        $this->assertEquals('active', $option->getName());
    }

    /** @test */
    public function it_can_be_created_with_constructor(): void
    {
        $option = new FilterOption('inactive');

        $this->assertEquals('inactive', $option->getName());
    }

    /** @test */
    public function it_generates_label_from_name_if_not_set(): void
    {
        $option = FilterOption::make('is_active');

        $this->assertEquals('Is active', $option->getLabel());
    }

    /** @test */
    public function it_can_set_label(): void
    {
        $option = FilterOption::make('active')
            ->label('Active Users');

        $this->assertEquals('Active Users', $option->getLabel());
    }

    /** @test */
    public function it_can_set_icon(): void
    {
        $option = FilterOption::make('active')
            ->icon('check-circle');

        $this->assertEquals('check-circle', $option->getIcon());
    }

    /** @test */
    public function it_returns_null_icon_by_default(): void
    {
        $option = FilterOption::make('active');

        $this->assertNull($option->getIcon());
    }

    /** @test */
    public function it_can_set_count_callback(): void
    {
        $option = FilterOption::make('active')
            ->count(fn() => 42);

        $this->assertEquals(42, $option->getCount());
    }

    /** @test */
    public function it_returns_null_count_by_default(): void
    {
        $option = FilterOption::make('active');

        $this->assertNull($option->getCount());
    }

    /** @test */
    public function it_can_use_dynamic_count(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com', 'status' => 'active']);
        TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com', 'status' => 'active']);
        TestModel::create(['name' => 'User 3', 'email' => 'user3@example.com', 'status' => 'inactive']);

        $option = FilterOption::make('active')
            ->count(fn() => TestModel::where('status', 'active')->count());

        $this->assertEquals(2, $option->getCount());
    }

    /** @test */
    public function it_can_set_query_callback(): void
    {
        $option = FilterOption::make('active')
            ->query(fn($query) => $query->where('status', 'active'));

        $this->assertInstanceOf(FilterOption::class, $option);
    }

    /** @test */
    public function it_can_apply_query_to_builder(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'Active User', 'email' => 'active@example.com', 'status' => 'active']);
        TestModel::create(['name' => 'Inactive User', 'email' => 'inactive@example.com', 'status' => 'inactive']);

        $option = FilterOption::make('active')
            ->query(fn($q) => $q->where('status', 'active'));

        $query = TestModel::query();
        $option->applyToQuery($query, 'active');

        // The option uses orWhere wrapping, so we need to test it properly
        $this->assertStringContainsString('status', $query->toSql());
    }

    /** @test */
    public function it_does_nothing_when_no_query_callback(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
        TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

        $option = FilterOption::make('active');

        $query = TestModel::query();
        $option->applyToQuery($query, 'active');

        $this->assertEquals(2, $query->count());
    }

    /** @test */
    public function it_can_convert_to_array(): void
    {
        $option = FilterOption::make('active')
            ->label('Active')
            ->icon('check')
            ->count(fn() => 10);

        $array = $option->toArray();

        $this->assertEquals('active', $array['name']);
        $this->assertEquals('Active', $array['label']);
        $this->assertEquals('check', $array['icon']);
        $this->assertEquals(10, $array['count']);
    }

    /** @test */
    public function it_converts_to_array_with_null_count(): void
    {
        $option = FilterOption::make('active')
            ->label('Active');

        $array = $option->toArray();

        $this->assertNull($array['count']);
    }

    /** @test */
    public function it_supports_fluent_api(): void
    {
        $option = FilterOption::make('pending')
            ->label('Pending Review')
            ->icon('clock')
            ->count(fn() => 5)
            ->query(fn($q) => $q->where('status', 'pending'));

        $this->assertInstanceOf(FilterOption::class, $option);
        $this->assertEquals('Pending Review', $option->getLabel());
        $this->assertEquals('clock', $option->getIcon());
        $this->assertEquals(5, $option->getCount());
    }
}
