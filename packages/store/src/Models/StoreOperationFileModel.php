<?php

namespace Packages\Store\Models;

use App\Models\BaseModel;

class StoreOperationFileModel extends BaseModel {

    protected $connection = 'main';
    protected $table = 'storeOperationFiles';

    public $timestamps = false;

    protected $fillable = [
        'storeOperationId',
        'name',
        'src',
    ];
}
