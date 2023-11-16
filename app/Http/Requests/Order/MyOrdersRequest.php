<?php

namespace App\Http\Requests\Order;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;


class MyOrdersRequest extends BaseApiRequest {

    public function rules() {

        return array_merge( parent::rules(),  [
            'perPage' => 'integer|min:1|max:1000',
            'page' => 'integer|min:1|max:' . DbHelper::MAX_INT,
        ]);
    }
}
