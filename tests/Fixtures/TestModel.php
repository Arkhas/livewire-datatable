<?php

namespace Arkhas\LivewireDatatable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestModel extends Model
{
    protected $table = 'test_models';

    protected $fillable = ['name', 'email', 'status'];

    public function relatedModels(): HasMany
    {
        return $this->hasMany(TestRelatedModel::class);
    }
}
