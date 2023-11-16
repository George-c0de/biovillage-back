<?php

namespace Packages\Store\Http\Requests\StoreOperation;

use App\Helpers\DbHelper;
use Packages\Store\Models\StoreModel;

class PutRequest extends StoreRequest
{
    public function rules()
    {
        return array_merge(parent::rules(),[
            'contents.*.netCost' => 'required|integer|min:0|max:'.DbHelper::MAX_INT,
        ]);
    }
}
