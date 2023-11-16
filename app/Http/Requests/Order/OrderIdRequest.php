<?php

namespace App\Http\Requests\Order;

use App\Helpers\DbHelper;
use App\Http\Requests\CheckIdRequest;


class OrderIdRequest extends CheckIdRequest {

    public function rules() {
        return [
            'id' => 'bail|required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:orders,id'
        ];
    }
}
