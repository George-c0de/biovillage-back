<?php

namespace Packages\Store\Http\Requests\StoreOperation;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\OrderModel;

class OrderingPuckRequest extends BaseApiRequest
{
    /**
     * Правила
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'id' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:orders,id',
                'items' => 'array',
                'items.*.id' =>
                    'bail|required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:orderItems,id,orderId,' . $this->id,
                'items.*.realUnits' => 'required|integer|min:0|max:' . DbHelper::MAX_INT,
            ]
        );
    }

    /**
     * Addition validation order statuses
     * @param $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if($validator->errors()->count() > 0) {
                return;
            }
            $order = OrderModel::findOrFail($this->id);
            if(!in_array($order->status, [OrderModel::STATUS_PACKED, OrderModel::STATUS_PLACED])) {
                $validator->errors()->add('id', trans('errors.wrongOrderStatus'));
            }
        });
    }
}
