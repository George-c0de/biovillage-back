<?php

namespace Packages\Store\Http\Requests\StoreOperation;

use App\Helpers\DbHelper;

class CorrectionRequest extends StoreRequest
{
    public function rules()
    {
        return array_merge(parent::rules(),[
            'contents.*.netCost' => 'required|integer|min:0|max:'.DbHelper::MAX_INT,
        ]);
    }
}
