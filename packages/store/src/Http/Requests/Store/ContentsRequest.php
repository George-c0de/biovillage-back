<?php

namespace Packages\Store\Http\Requests\Store;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\OrderModel;
use App\Models\ProductModel;
use Packages\Store\Models\StoreModel;

class ContentsRequest extends BaseApiRequest
{

    public function rules()
    {
        return [
            'id' => sprintf(
                'bail|required|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new StoreModel)->getTable()
            ),

            'perPage' => 'integer|min:1|max:1000',
            'page' => 'integer|min:1|max:' . DbHelper::MAX_INT,

            'productId' => sprintf(
                'bail|nullable|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new ProductModel)->getTable()
            ),

            'orderId' => sprintf(
                'bail|nullable|integer|exists:%s,id|min:1|max:'.DbHelper::MAX_INT,
                with(new OrderModel)->getTable()
            ),

            'createdAtBegin' => 'nullable|date_format:' . locale()->dateFormat,
            'createdAtEnd' => 'nullable|date_format:' . locale()->dateFormat,

            'updatedAtBegin' => 'nullable|date_format:' . locale()->dateFormat,
            'updatedAtEnd' => 'nullable|date_format:' . locale()->dateFormat,
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'id' => $this['id'],
        ]);
    }
}
