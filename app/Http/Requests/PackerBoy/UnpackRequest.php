<?php

namespace App\Http\Requests\PackerBoy;

use App\Helpers\DbHelper;
use App\Http\Requests\Order\OrderIdRequest;
use App\Models\OrderModel;


class UnpackRequest extends OrderIdRequest {


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
            if($order->status != OrderModel::STATUS_PACKED) {
                $validator->errors()->add('id', trans('errors.orderIsPacked'));
            }
        });
    }
}
