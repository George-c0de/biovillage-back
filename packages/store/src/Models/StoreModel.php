<?php

namespace Packages\Store\Models;

use App\Locale\BaseFormatter;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreModel extends BaseModel {
    use SoftDeletes;

    protected $connection = 'main';
    protected $table = 'stores';

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';
    public const DELETED_AT = 'deletedAt';

    const TYPE_PRODUCT = 'product';
    const TYPE_GIFT = 'gift';
    const TYPES = [
        self::TYPE_PRODUCT,
        self::TYPE_GIFT,
    ];

    protected $dates = [
        'createdAt',
        'updatedAt',
        'deletedAt',
    ];

    protected $fillable = [
        'name',
        'type',
        'order',
        'systemPlaceId',
        'createdAt',
        'updatedAt',
        'deletedAt',
    ];
}
