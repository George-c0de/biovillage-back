<?php

namespace Packages\Store\Models;

use App\Models\BaseModel;

class StoreOperationModel extends BaseModel {

    // type operations
    const PUT_TYPE = 'put';
    const TAKE_TYPE = 'take';
    const CORRECTION_TYPE = 'correction';
    const RESET_CORRECTION_TYPE = 'resetCorrection';
    const ORDERING_TYPE = 'ordering';

    const SORT_CREATED = 'createdAt';
    const SORT_ID = 'id';
    const SORTS = [
        self::SORT_CREATED,
        self::SORT_ID,
    ];

    const MAX_FILE_SIZE = 4999;

    const TYPES = [
        self::PUT_TYPE,
        self::TAKE_TYPE,
        self::CORRECTION_TYPE,
        self::ORDERING_TYPE,
    ];

    // statuses
    const COMPLETED_STATUS = 'completed';
    const RESERVE_STATUS = 'reserve';
    const CANCEL_STATUS = 'cancel';

    const STATUSES = [
        self::COMPLETED_STATUS,
        self::RESERVE_STATUS,
        self::CANCEL_STATUS,
    ];

    protected $connection = 'main';
    protected $table = 'storeOperations';

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';

    protected $dates = [
        'createdAt',
        'updatedAt',
    ];

    protected $fillable = [
        'type',
        'storeIds',
        'isMultipleStores',
        'status',
        'adminId',
        'clientId',
        'orderId',
        'comment',
        'createdAt',
    ];
}
