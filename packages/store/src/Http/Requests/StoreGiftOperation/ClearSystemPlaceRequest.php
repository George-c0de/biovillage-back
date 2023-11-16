<?php

namespace Packages\Store\Http\Requests\StoreGiftOperation;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use Packages\Store\Models\StoreModel;

class ClearSystemPlaceRequest extends BaseApiRequest
{
    public function rules()
    {
        return [
            'storeId' => sprintf(
                'bail|required|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new StoreModel)->getTable()
            ),
        ];
    }
}
