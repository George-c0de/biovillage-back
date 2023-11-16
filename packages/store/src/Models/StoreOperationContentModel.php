<?php

namespace Packages\Store\Models;

use App\Models\BaseModel;

class StoreOperationContentModel extends BaseModel {

    protected $primaryKey = null;
    public $incrementing = false;

    const CONDITION_DEFAULT = 5;

    protected $connection = 'main';
    protected $table = 'storeOperationContents';

    public $timestamps = false;

    protected $fillable = [
        'storeId',
        'storeOperationId',
        'productId',
        'giftId',
        'unitId',
        'storePlaceId',
        'quantity',
        'netCost',
        'netCostPerStep',
    ];
}
