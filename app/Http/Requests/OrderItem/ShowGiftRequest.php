<?php

namespace App\Http\Requests\OrderItem;

use App\Helpers\DbHelper;
use App\Http\Requests\CheckIdRequest;

class ShowGiftRequest extends CheckIdRequest {

    public function rules() {
        return [
            'id' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:orderGifts,id',
        ];
    }
}
