<?php

namespace Packages\Store\Http\Requests\StoreOperation;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\Auth\Admin;
use App\Models\Auth\Client;
use App\Models\OrderModel;
use Illuminate\Validation\Rule;
use Packages\Store\Models\StoreModel;
use Packages\Store\Models\StoreOperationModel;

class IndexRequest extends BaseApiRequest
{

    public function rules()
    {
        return [
            'storeId' => sprintf(
                'bail|required|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new StoreModel)->getTable()
            ),

            'perPage' => 'integer|min:1|max:1000',
            'page' => 'integer|min:1|max:' . DbHelper::MAX_INT,

            'adminId' => sprintf(
                'bail|nullable|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new Admin)->getTable()
            ),

            'clientId' => sprintf(
                'bail|nullable|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new Client)->getTable()
            ),

            'type' => [
                'nullable',
                Rule::in(StoreOperationModel::TYPES),
            ],

            'orderId' => sprintf(
                'bail|nullable|integer|exists:%s,id|min:1|max:'.DbHelper::MAX_INT,
                with(new OrderModel)->getTable()
            ),

            'createdAtBegin' => 'nullable|date_format:' . locale()->dateFormat,
            'createdAtEnd' => 'nullable|date_format:' . locale()->dateFormat,

            'updatedAtBegin' => 'nullable|date_format:' . locale()->dateFormat,
            'updatedAtEnd' => 'nullable|date_format:' . locale()->dateFormat,

            'sort' => [ Rule::in(StoreOperationModel::SORTS) ],
            'sortDirect' => [ Rule::in(['asc', 'desc']) ],

        ];
    }
}
