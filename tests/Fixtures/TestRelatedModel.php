<?php

namespace Arkhas\LivewireDatatable\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestRelatedModel extends Model
{
    protected $table = 'test_related_models';

    protected $fillable = ['test_model_id', 'title'];

    public function testModel(): BelongsTo
    {
        return $this->belongsTo(TestModel::class);
    }
}
