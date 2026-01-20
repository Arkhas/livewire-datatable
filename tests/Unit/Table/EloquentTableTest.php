<?php

namespace Arkhas\LivewireDatatable\Tests\Unit\Table;

use Arkhas\LivewireDatatable\Table\EloquentTable;
use Arkhas\LivewireDatatable\Columns\Column;
use Arkhas\LivewireDatatable\Columns\ActionColumn;
use Arkhas\LivewireDatatable\Columns\CheckboxColumn;
use Arkhas\LivewireDatatable\Actions\TableAction;
use Arkhas\LivewireDatatable\Filters\Filter;
use Arkhas\LivewireDatatable\Filters\FilterOption;
use Arkhas\LivewireDatatable\Tests\TestCase;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestRelatedModel;

class EloquentTableTest extends TestCase
{
    protected function defineDatabaseMigrations(): void
    {
        parent::defineDatabaseMigrations();
    }

    protected function createTable(): EloquentTable
    {
        return new EloquentTable(TestModel::query());
    }

    /** @test */
    public function it_can_be_created(): void
    {
        $table = $this->createTable();

        $this->assertInstanceOf(EloquentTable::class, $table);
    }

    /** @test */
    public function it_can_get_query(): void
    {
        $table = $this->createTable();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $table->getQuery());
    }

    /** @test */
    public function it_clones_query(): void
    {
        $table = $this->createTable();

        $query1 = $table->getQuery();
        $query2 = $table->getQuery();

        $this->assertNotSame($query1, $query2);
    }

    /** @test */
    public function it_can_get_model_class(): void
    {
        $table = $this->createTable();

        $this->assertEquals(TestModel::class, $table->getModel());
    }

    /** @test */
    public function it_can_set_and_get_export_name(): void
    {
        $table = $this->createTable()
            ->exportName('users_export');

        $this->assertEquals('users_export', $table->getExportName());
    }

    /** @test */
    public function it_has_default_export_name(): void
    {
        $table = $this->createTable();

        $this->assertEquals('export', $table->getExportName());
    }

    /** @test */
    public function it_can_build_query_without_modifications(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
        TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

        $table = $this->createTable()
            ->columns([
                Column::make('name'),
                Column::make('email'),
            ]);

        $query = $table->buildQuery();

        $this->assertEquals(2, $query->count());
    }

    /** @test */
    public function it_can_build_query_with_search(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'John Doe', 'email' => 'john@example.com']);
        TestModel::create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        $table = $this->createTable()
            ->columns([
                Column::make('name'),
                Column::make('email'),
            ])
            ->searchable(['name']);

        $query = $table->buildQuery([], 'John');

        $this->assertEquals(1, $query->count());
        $this->assertEquals('John Doe', $query->first()->name);
    }

    /** @test */
    public function it_can_search_from_columns(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'Test User', 'email' => 'test@example.com']);
        TestModel::create(['name' => 'Another User', 'email' => 'another@example.com']);

        $table = $this->createTable()
            ->columns([
                Column::make('name'),
                Column::make('email'),
            ])
            ->searchable();

        $query = $table->buildQuery([], 'test');

        $this->assertEquals(1, $query->count());
    }

    /** @test */
    public function it_excludes_special_columns_from_search(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);

        $table = $this->createTable()
            ->columns([
                CheckboxColumn::make(),
                Column::make('name'),
                ActionColumn::make(),
            ])
            ->searchable();

        // Should not throw error and should work properly
        $query = $table->buildQuery([], 'User');

        $this->assertEquals(1, $query->count());
    }

    /** @test */
    public function it_can_search_relations(): void
    {
        $this->defineDatabaseMigrations();
        
        $model = TestModel::create(['name' => 'Parent', 'email' => 'parent@example.com']);
        TestRelatedModel::create(['test_model_id' => $model->id, 'title' => 'Child Title']);

        $table = new EloquentTable(TestModel::query()->with('relatedModels'))
            ->columns([
                Column::make('name'),
            ])
            ->searchable(['relatedModels.title']);

        $query = $table->buildQuery([], 'Child');

        $this->assertEquals(1, $query->count());
    }

    /** @test */
    public function it_does_not_search_when_disabled(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'Searchable', 'email' => 'search@example.com']);

        $table = $this->createTable()
            ->columns([Column::make('name')])
            ->notSearchable();

        $query = $table->buildQuery([], 'xyz');

        // Should return all results since search is disabled
        $this->assertEquals(1, $query->count());
    }

    /** @test */
    public function it_can_build_query_with_filters(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'Active User', 'email' => 'active@example.com', 'status' => 'active']);
        TestModel::create(['name' => 'Inactive User', 'email' => 'inactive@example.com', 'status' => 'inactive']);

        $table = $this->createTable()
            ->columns([Column::make('name')])
            ->filters([
                Filter::make('status')
                    ->options([
                        FilterOption::make('active')
                            ->query(fn($q) => $q->where('status', 'active')),
                    ]),
            ]);

        $query = $table->buildQuery(['status' => ['active']]);

        $this->assertEquals(1, $query->count());
        $this->assertEquals('Active User', $query->first()->name);
    }

    /** @test */
    public function it_can_apply_column_filters(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User A', 'email' => 'a@example.com', 'status' => 'active']);
        TestModel::create(['name' => 'User B', 'email' => 'b@example.com', 'status' => 'inactive']);

        $table = $this->createTable()
            ->columns([
                Column::make('name'),
                Column::make('status')
                    ->filter(fn($query, $value) => $query->where('status', $value)),
            ]);

        $query = $table->buildQuery(['status' => 'active']);

        $this->assertEquals(1, $query->count());
    }

    /** @test */
    public function it_can_build_query_with_sorting(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'Zebra', 'email' => 'z@example.com']);
        TestModel::create(['name' => 'Apple', 'email' => 'a@example.com']);
        TestModel::create(['name' => 'Mango', 'email' => 'm@example.com']);

        $table = $this->createTable()
            ->columns([
                Column::make('name')->sortable(),
            ]);

        $query = $table->buildQuery([], null, 'name', 'asc');
        $results = $query->get();

        $this->assertEquals('Apple', $results->first()->name);
        $this->assertEquals('Zebra', $results->last()->name);
    }

    /** @test */
    public function it_can_sort_descending(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'Zebra', 'email' => 'z@example.com']);
        TestModel::create(['name' => 'Apple', 'email' => 'a@example.com']);

        $table = $this->createTable()
            ->columns([
                Column::make('name')->sortable(),
            ]);

        $query = $table->buildQuery([], null, 'name', 'desc');
        $results = $query->get();

        $this->assertEquals('Zebra', $results->first()->name);
    }

    /** @test */
    public function it_uses_custom_sort_column(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'Test', 'email' => 'z@example.com']);
        TestModel::create(['name' => 'Test', 'email' => 'a@example.com']);

        $table = $this->createTable()
            ->columns([
                Column::make('name')
                    ->sortable()
                    ->sortBy('email'),
            ]);

        $query = $table->buildQuery([], null, 'name', 'asc');
        $results = $query->get();

        $this->assertEquals('a@example.com', $results->first()->email);
    }

    /** @test */
    public function it_does_not_sort_non_sortable_columns(): void
    {
        $this->defineDatabaseMigrations();
        
        $user1 = TestModel::create(['name' => 'Zebra', 'email' => 'z@example.com']);
        $user2 = TestModel::create(['name' => 'Apple', 'email' => 'a@example.com']);

        $table = $this->createTable()
            ->columns([
                Column::make('name')->sortable(false),
            ]);

        $query = $table->buildQuery([], null, 'name', 'asc');
        $results = $query->get();

        // Should be in insertion order since sorting is disabled
        $this->assertEquals($user1->id, $results->first()->id);
    }

    /** @test */
    public function it_ignores_sorting_for_unknown_column(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'User', 'email' => 'user@example.com']);

        $table = $this->createTable()
            ->columns([
                Column::make('name'),
            ]);

        // Should not throw error
        $query = $table->buildQuery([], null, 'unknown_column', 'asc');

        $this->assertEquals(1, $query->count());
    }

    /** @test */
    public function it_can_paginate_results(): void
    {
        $this->defineDatabaseMigrations();
        
        for ($i = 1; $i <= 25; $i++) {
            TestModel::create(['name' => "User $i", 'email' => "user$i@example.com"]);
        }

        $table = $this->createTable()
            ->columns([Column::make('name')]);

        $paginated = $table->paginate([], null, null, 'asc', 10);

        $this->assertEquals(10, $paginated->count());
        $this->assertEquals(25, $paginated->total());
        $this->assertEquals(3, $paginated->lastPage());
    }

    /** @test */
    public function it_can_convert_to_array(): void
    {
        $table = $this->createTable()
            ->columns([
                Column::make('name'),
                Column::make('email'),
            ])
            ->filters([
                Filter::make('status'),
            ])
            ->actions([
                TableAction::make('delete'),
            ])
            ->exportName('users')
            ->searchable(['name']);

        $array = $table->toArray();

        $this->assertArrayHasKey('columns', $array);
        $this->assertArrayHasKey('filters', $array);
        $this->assertArrayHasKey('actions', $array);
        $this->assertArrayHasKey('exportName', $array);
        $this->assertArrayHasKey('searchable', $array);
        $this->assertCount(2, $array['columns']);
        $this->assertCount(1, $array['filters']);
        $this->assertCount(1, $array['actions']);
        $this->assertEquals('users', $array['exportName']);
        $this->assertTrue($array['searchable']);
    }

    /** @test */
    public function it_combines_all_query_modifications(): void
    {
        $this->defineDatabaseMigrations();
        
        TestModel::create(['name' => 'Active John', 'email' => 'john@example.com', 'status' => 'active']);
        TestModel::create(['name' => 'Active Jane', 'email' => 'jane@example.com', 'status' => 'active']);
        TestModel::create(['name' => 'Inactive Bob', 'email' => 'bob@example.com', 'status' => 'inactive']);

        $table = $this->createTable()
            ->columns([
                Column::make('name')->sortable(),
            ])
            ->filters([
                Filter::make('status')
                    ->options([
                        FilterOption::make('active')
                            ->query(fn($q) => $q->where('status', 'active')),
                    ]),
            ])
            ->searchable(['name']);

        $query = $table->buildQuery(
            ['status' => ['active']],
            'Jane',
            'name',
            'asc'
        );

        $this->assertEquals(1, $query->count());
        $this->assertEquals('Active Jane', $query->first()->name);
    }
}
