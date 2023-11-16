<?php

namespace App\Http\Requests\OrderItem;

use App\Helpers\DbHelper;

class UpdateGiftRequest extends ShowGiftRequest {

    public function rules() {
        return array_merge( parent::rules(), [
            'realQty' => 'integer|min:0|max:' . DbHelper::MAX_INT,
            'realTotalBonuses' => 'integer|min:0|max:' . DbHelper::MAX_INT,
        ]);
    }
}
