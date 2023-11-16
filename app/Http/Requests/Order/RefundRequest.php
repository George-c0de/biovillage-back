<?php

namespace App\Http\Requests\Order;

use App\Helpers\DbHelper;
use App\Http\Requests\CheckIdRequest;
use App\Models\OrderModel;
use Illuminate\Validation\Rule;


class RefundRequest extends CheckIdRequest {

    #TODO Написать понятные названия полей и причины ошибок

    public function rules() {
        return [
            'id' => [
                'required',
                'integer',
                'min:1',
                'max:' . DbHelper::MAX_INT,
                Rule::exists('orders', 'id')
                    ->where('status', OrderModel::STATUS_FINISHED)
            ]
        ];
    }
}
