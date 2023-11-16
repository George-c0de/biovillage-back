<?php

namespace Packages\Store\Http\Requests\StorePlace;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use Packages\Store\Models\StoreModel;

class StoreRequest extends BaseApiRequest
{

    public function rules()
    {
        return [
            'storeId' => sprintf(
                'bail|required|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new StoreModel)->getTable()
            ),
            'name' => 'required|string|min:3|max:255',
            'order' => 'nullable|integer|min:1|max:'.DbHelper::MAX_INT,
        ];
    }
}
