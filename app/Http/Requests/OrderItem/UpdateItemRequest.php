<?php

namespace App\Http\Requests\OrderItem;

use App\Helpers\DbHelper;

class UpdateItemRequest extends ShowItemRequest {

    public function rules() {
        return array_merge( parent::rules(), [
            'realPrice' => 'integer|min:0|max:' . DbHelper::MAX_INT,
            'realUnits' => 'integer|min:0|max:' . DbHelper::MAX_INT,
            'realTotal' => 'integer|min:0|max:' . DbHelper::MAX_INT,
        ]);
    }
}
