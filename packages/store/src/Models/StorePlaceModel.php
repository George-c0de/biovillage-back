<?php

namespace Packages\Store\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorePlaceModel extends BaseModel {
    use SoftDeletes;

    protected $connection = 'main';
    protected $table = 'storePlaces';

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';
    public const DELETED_AT = 'deletedAt';

    protected $dates = [
        'createdAt',
        'updatedAt',
        'deletedAt',
    ];

    protected $fillable = [
        'storeId',
        'name',
        'order',
        'isSystem',
        'createdAt',
        'updatedAt',
        'deletedAt',
    ];
}
