<?php

namespace App\Http\Requests\PackerBoy;

use App\Helpers\DbHelper;
use App\Http\Requests\Order\OrderIdRequest;
use App\Models\OrderModel;


class PackRequest extends OrderIdRequest {


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
                'force' => 'nullable|boolean',
                'items' => 'array',
                'items.*.id' =>
                    'bail|required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:orderItems,id,orderId,' . $this->id,
                'items.*.productId' =>
                    'bail|required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:products,id',
                'items.*.realPrice' => 'required|integer|min:0|max:' . DbHelper::MAX_INT,
                'items.*.realUnits' => 'required|integer|min:0|max:' . DbHelper::MAX_INT,
                'items.*.realTotal' => 'required|integer|min:0|max:' . DbHelper::MAX_INT,

                'gifts' => 'array',
                'gifts.*.id' =>
                    'bail|required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:orderGifts,id,orderId,' . $this->id,
                'gifts.*.giftId' =>
                    'bail|required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:gifts,id',
                'gifts.*.realQty' => 'required|integer|min:0|max:' . DbHelper::MAX_INT,
                'gifts.*.realTotalBonuses' => 'required|integer|min:0|max:' . DbHelper::MAX_INT,
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

    protected function prepareForValidation() {
        $this->merge([ 'id' => $this['id'], ]);
    }
}
