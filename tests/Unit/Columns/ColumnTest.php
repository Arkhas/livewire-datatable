<?php

use Arkhas\LivewireDatatable\Columns\Column;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestModel;
use Arkhas\LivewireDatatable\Tests\Fixtures\TestRelatedModel;

test('it can be created with make', function () {
    $column = Column::make('name');

    expect($column)
        ->toBeInstanceOf(Column::class)
        ->and($column->getName())->toBe('name');
});

test('it can be created with constructor', function () {
    $column = new Column('email');

    expect($column->getName())->toBe('email');
});

test('it generates label from name if not set', function () {
    $column = Column::make('first_name');

    expect($column->getLabel())->toBe('First name');
});

test('it handles dot notation in label generation', function () {
    $column = Column::make('user.name');

    expect($column->getLabel())->toBe('User name');
});

test('it can set and get label', function () {
    $column = Column::make('name')
        ->label('Full Name');

    expect($column->getLabel())->toBe('Full Name');
});

test('it can set and get width', function () {
    $column = Column::make('name')
        ->width('200px');

    expect($column->getWidth())->toBe('200px');
});

test('it returns null width by default', function () {
    $column = Column::make('name');

    expect($column->getWidth())->toBeNull();
});

test('it is sortable by default', function () {
    $column = Column::make('name');

    expect($column->isSortable())->toBeTrue();
});

test('it can disable sorting', function () {
    $column = Column::make('name')
        ->sortable(false);

    expect($column->isSortable())->toBeFalse();
});

test('it can enable sorting', function () {
    $column = Column::make('name')
        ->sortable(false)
        ->sortable(true);

    expect($column->isSortable())->toBeTrue();
});

test('it uses name as default sort column', function () {
    $column = Column::make('email');

    expect($column->getSortColumn())->toBe('email');
});

test('it can set custom sort column', function () {
    $column = Column::make('name')
        ->sortBy('users.name');

    expect($column->getSortColumn())->toBe('users.name');
});

test('it is toggable by default', function () {
    $column = Column::make('name');

    expect($column->isToggable())->toBeTrue();
});

test('it can disable toggable', function () {
    $column = Column::make('name')
        ->toggable(false);

    expect($column->isToggable())->toBeFalse();
});

test('it is not hidden by default', function () {
    $column = Column::make('name');

    expect($column->isHidden())->toBeFalse();
});

test('it can be hidden', function () {
    $column = Column::make('name')
        ->hidden();

    expect($column->isHidden())->toBeTrue();
});

test('it can be shown after hidden', function () {
    $column = Column::make('name')
        ->hidden()
        ->hidden(false);

    expect($column->isHidden())->toBeFalse();
});

test('it can get html content from model', function () {
    $model = TestModel::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $column = Column::make('name');

    expect($column->getHtml($model))->toBe('John Doe');
});

test('it escapes html content', function () {
    $model = TestModel::create([
        'name' => '<script>alert("xss")</script>',
        'email' => 'test@example.com',
    ]);

    $column = Column::make('name');

    expect($column->getHtml($model))->toBe('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;');
});

test('it can use custom html callback', function () {
    $model = TestModel::create([
        'name' => 'John',
        'email' => 'john@example.com',
    ]);

    $column = Column::make('name')
        ->html(fn($model) => '<strong>' . $model->name . '</strong>');

    expect($column->getHtml($model))->toBe('<strong>John</strong>');
});

test('it can get value from model', function () {
    $model = TestModel::create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);

    $column = Column::make('email');

    expect($column->getValue($model))->toBe('jane@example.com');
});

test('it can get value with dot notation for single relation', function () {
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

    expect($column->getValue($related))->toBe('Test User');
});

test('it returns null for missing relation', function () {
    $model = TestModel::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $column = Column::make('missingRelation.field');

    expect($column->getValue($model))->toBeNull();
});

test('it returns empty string for null value', function () {
    $model = TestModel::create([
        'name' => 'Test User',
        'email' => null,
    ]);

    $column = Column::make('email');

    expect($column->getHtml($model))->toBe('');
});

test('it does not have icon by default', function () {
    $column = Column::make('name');

    expect($column->hasIcon())->toBeFalse();
});

test('it can set icon callback', function () {
    $column = Column::make('status')
        ->icon(fn($model) => $model->status === 'active' ? 'check' : 'x');

    expect($column->hasIcon())->toBeTrue();
});

test('it can get icon from model', function () {
    $model = TestModel::create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'status' => 'active',
    ]);

    $column = Column::make('status')
        ->icon(fn($model) => $model->status === 'active' ? 'check-circle' : 'x-circle');

    expect($column->getIcon($model))->toBe('check-circle');
});

test('it returns null icon when no callback', function () {
    $model = TestModel::create([
        'name' => 'Test',
        'email' => 'test@example.com',
    ]);

    $column = Column::make('name');

    expect($column->getIcon($model))->toBeNull();
});

test('it does not have filter by default', function () {
    $column = Column::make('name');

    expect($column->hasFilter())->toBeFalse();
});

test('it can set filter callback', function () {
    $column = Column::make('status')
        ->filter(fn($query, $value) => $query->where('status', $value));

    expect($column->hasFilter())->toBeTrue();
});

test('it can apply filter to query', function () {
    TestModel::create(['name' => 'Active User', 'email' => 'active@example.com', 'status' => 'active']);
    TestModel::create(['name' => 'Inactive User', 'email' => 'inactive@example.com', 'status' => 'inactive']);

    $column = Column::make('status')
        ->filter(fn($query, $value) => $query->where('status', $value));

    $query = TestModel::query();
    $column->applyFilter($query, 'active');

    expect($query->count())->toBe(1)
        ->and($query->first()->name)->toBe('Active User');
});

test('it does nothing when applying filter without callback', function () {
    TestModel::create(['name' => 'User 1', 'email' => 'user1@example.com']);
    TestModel::create(['name' => 'User 2', 'email' => 'user2@example.com']);

    $column = Column::make('name');

    $query = TestModel::query();
    $column->applyFilter($query, 'some value');

    expect($query->count())->toBe(2);
});

test('it can set export callback', function () {
    $column = Column::make('status')
        ->exportAs(fn($model) => strtoupper($model->status));

    expect($column)->toBeInstanceOf(Column::class);
});

test('it can get export value with callback', function () {
    $model = TestModel::create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'status' => 'active',
    ]);

    $column = Column::make('status')
        ->exportAs(fn($model) => strtoupper($model->status));

    expect($column->getExportValue($model))->toBe('ACTIVE');
});

test('it uses value as export value without callback', function () {
    $model = TestModel::create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'status' => 'active',
    ]);

    $column = Column::make('status');

    expect($column->getExportValue($model))->toBe('active');
});

test('it can be converted to array', function () {
    $column = Column::make('email')
        ->label('Email Address')
        ->width('150px')
        ->sortable(true)
        ->toggable(false)
        ->hidden(true)
        ->icon(fn($model) => 'mail');

    $array = $column->toArray();

    expect($array)->toBe([
        'name' => 'email',
        'label' => 'Email Address',
        'width' => '150px',
        'sortable' => true,
        'toggable' => false,
        'hidden' => true,
        'hasIcon' => true,
        'hasFilter' => false,
        'type' => 'column',
    ]);
});

test('it supports fluent api', function () {
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

    expect($column)->toBeInstanceOf(Column::class)
        ->and($column->getLabel())->toBe('Name')
        ->and($column->getWidth())->toBe('100px')
        ->and($column->isSortable())->toBeTrue()
        ->and($column->isToggable())->toBeTrue()
        ->and($column->isHidden())->toBeTrue()
        ->and($column->getSortColumn())->toBe('full_name')
        ->and($column->hasIcon())->toBeTrue()
        ->and($column->hasFilter())->toBeTrue();
});

test('it can use blade callback to render blade template', function () {
    $model = TestModel::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $column = Column::make('name')
        ->blade(fn($model) => '{{ $model->name }}');

    $html = $column->getHtml($model);

    expect($html)->toBe('John Doe');
});

test('it can use blade callback with model variable name', function () {
    $model = TestModel::create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);

    $column = Column::make('name')
        ->blade(fn(TestModel $testModel) => '{{ $testModel->name }}');

    $html = $column->getHtml($model);

    expect($html)->toBe('Jane Doe');
});

test('it can use blade callback with html tags', function () {
    $model = TestModel::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    $column = Column::make('name')
        ->blade(fn($model) => '<strong>{{ $model->name }}</strong>');

    $html = $column->getHtml($model);

    expect($html)->toBe('<strong>Test User</strong>');
});

test('blade callback has priority over html callback', function () {
    $model = TestModel::create([
        'name' => 'Priority Test',
        'email' => 'test@example.com',
    ]);

    $column = Column::make('name')
        ->html(fn($model) => '<em>HTML</em>')
        ->blade(fn($model) => '<strong>{{ $model->name }}</strong>');

    $html = $column->getHtml($model);

    expect($html)->toBe('<strong>Priority Test</strong>');
});

test('it can use blade callback with multiple variables', function () {
    $model = TestModel::create([
        'name' => 'Multi Var',
        'email' => 'multi@example.com',
    ]);

    $column = Column::make('name')
        ->blade(fn($model) => '{{ $model->name }} - {{ $testModel->email }}');

    $html = $column->getHtml($model);

    expect($html)->toBe('Multi Var - multi@example.com');
});
