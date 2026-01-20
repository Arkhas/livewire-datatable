<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Filters;

use Arkhas\LivewireDatatable\Filters\Filter;
use Arkhas\LivewireDatatable\Filters\FilterOption;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;

class FilterTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        parent::defineDatabaseMigrations();
    }

    /** @test */
    public function it_can_be_created_with_make(): void
    {
        $filter = Filter::make('status');

        $this->assertInstanceOf(Filter::class, $filter);
        $this->assertEquals('status', $filter->getName());
    }

    /** @test */
    public function it_can_be_created_with_constructor(): void
    {
        $filter = new Filter('category');

        $this->assertEquals('category', $filter->getName());
    }

    /** @test */
    public function it_generates_label_from_name_if_not_set(): void
    {
        $filter = Filter::make('account_status');

        $this->assertEquals('Account status', $filter->getLabel());
    }

    /** @test */
    public function it_can_set_label(): void
    {
        $filter = Filter::make('status')
            ->label('Account Status');

        $this->assertEquals('Account Status', $filter->getLabel());
    }

    /** @test */
    public function it_is_not_multiple_by_default(): void
    {
        $filter = Filter::make('status');

        $this->assertFalse($filter->isMultiple());
    }

    /** @test */
    public function it_can_enable_multiple_selection(): void
    {
        $filter = Filter::make('status')
            ->multiple();

        $this->assertTrue($filter->isMultiple());
    }

    /** @test */
    public function it_can_disable_multiple_selection(): void
    {
        $filter = Filter::make('status')
            ->multiple()
            ->multiple(false);

        $this->assertFalse($filter->isMultiple());
    }

    /** @test */
    public function it_can_set_options(): void
    {
        $options = [
            FilterOption::make('active')->label('Active'),
            FilterOption::make('inactive')->label('Inactive'),
        ];

        $filter = Filter::make('status')
            ->options($options);

        $this->assertCount(2, $filter->getOptions());
        $this->assertSame($options, $filter->getOptions());
    }

    /** @test */
    public function it_returns_empty_options_by_default(): void
    {
        $filter = Filter::make('status');

        $this->assertEquals([], $filter->getOptions());
    }

    /** @test */
    public function it_can_get_option_by_name(): void
    {
        $activeOption = FilterOption::make('active');
        $inactiveOption = FilterOption::make('inactive');

        $filter = Filter::make('status')
            ->options([$activeOption, $inactiveOption]);

        $this->assertSame($activeOption, $filter->getOption('active'));
        $this->assertSame($inactiveOption, $filter->getOption('inactive'));
    }

    /** @test */
    public function it_returns_null_for_missing_option(): void
    {
        $filter = Filter::make('status')
            ->options([
                FilterOption::make('active'),
            ]);

        $this->assertNull($filter->getOption('pending'));
    }

    /** @test */
    public function it_can_set_global_query_callback(): void
    {
        $filter = Filter::make('status')
            ->query(fn($query, $values) => $query->whereIn('status', $values));

        $this->assertInstanceOf(Filter::class, $filter);
    }

    /** @test */
    public function it_does_not_apply_filter_with_empty_values(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com', 'status' => 'active']);
        TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com', 'status' => 'inactive']);

        $filter = Filter::make('status')
            ->options([
                FilterOption::make('active')
                    ->query(fn($q) => $q->where('status', 'active')),
            ]);

        $query = TestModel::query();
        $filter->applyToQuery($query, []);

        $this->assertEquals(2, $query->count());
    }

    /** @test */
    public function it_can_apply_option_queries(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'Active User', 'email' => 'active@example.com', 'status' => 'active']);
        TestModel::create(['name' => 'Inactive User', 'email' => 'inactive@example.com', 'status' => 'inactive']);

        $filter = Filter::make('status')
            ->options([
                FilterOption::make('active')
                    ->query(fn($q) => $q->where('status', 'active')),
                FilterOption::make('inactive')
                    ->query(fn($q) => $q->where('status', 'inactive')),
            ]);

        $query = TestModel::query();
        $filter->applyToQuery($query, ['active']);

        $this->assertEquals(1, $query->count());
        $this->assertEquals('Active User', $query->first()->name);
    }

    /** @test */
    public function it_applies_global_query_callback(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com', 'status' => 'active']);
        TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com', 'status' => 'inactive']);
        TestModel::create(['name' => 'User 3', 'email' => 'user3@example.com', 'status' => 'pending']);

        $filter = Filter::make('status')
            ->options([
                FilterOption::make('active'),
                FilterOption::make('inactive'),
            ])
            ->query(fn($query, $values) => $query->whereIn('status', $values));

        $query = TestModel::query();
        $filter->applyToQuery($query, ['active', 'inactive']);

        $this->assertEquals(2, $query->count());
    }

    /** @test */
    public function it_can_get_selected_count(): void
    {
        $filter = Filter::make('status');

        $this->assertEquals(0, $filter->getSelectedCount([]));
        $this->assertEquals(1, $filter->getSelectedCount(['active']));
        $this->assertEquals(3, $filter->getSelectedCount(['active', 'inactive', 'pending']));
    }

    /** @test */
    public function it_can_convert_to_array(): void
    {
        $filter = Filter::make('status')
            ->label('Status Filter')
            ->multiple()
            ->options([
                FilterOption::make('active')
                    ->label('Active')
                    ->icon('check'),
                FilterOption::make('inactive')
                    ->label('Inactive')
                    ->icon('x'),
            ]);

        $array = $filter->toArray();

        $this->assertEquals('status', $array['name']);
        $this->assertEquals('Status Filter', $array['label']);
        $this->assertTrue($array['multiple']);
        $this->assertCount(2, $array['options']);
        $this->assertEquals('active', $array['options'][0]['name']);
        $this->assertEquals('inactive', $array['options'][1]['name']);
    }

    /** @test */
    public function it_supports_fluent_api(): void
    {
        $filter = Filter::make('category')
            ->label('Category')
            ->multiple()
            ->options([
                FilterOption::make('tech'),
                FilterOption::make('sport'),
            ])
            ->query(fn($q, $v) => $q);

        $this->assertInstanceOf(Filter::class, $filter);
        $this->assertEquals('Category', $filter->getLabel());
        $this->assertTrue($filter->isMultiple());
        $this->assertCount(2, $filter->getOptions());
    }
}
