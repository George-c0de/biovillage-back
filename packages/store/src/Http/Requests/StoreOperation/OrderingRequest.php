<?php

namespace Packages\Store\Http\Requests\StoreOperation;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\OrderModel;

class OrderingRequest extends BaseApiRequest
{
    public function rules()
    {
        return array_merge(parent::rules(),[
            'orderId' => sprintf(
                'bail|required|integer|exists:%s,id|min:1|max:'.DbHelper::MAX_INT,
                with(new OrderModel)->getTable()
            ),
        ]);
    }
}
