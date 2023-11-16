<?php

namespace App\Http\Requests\OrderItem;

use App\Helpers\DbHelper;

class MassUpdateItemRequest extends ShowItemRequest {

    public function rules() {
        return [
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:orderItems,id',
            'items.*.realPrice' => 'required|integer|min:0|max:' . DbHelper::MAX_INT,
            'items.*.realUnits' => 'required|integer|min:0|max:' . DbHelper::MAX_INT,
            'items.*.realTotal' => 'required|integer|min:0|max:' . DbHelper::MAX_INT,
            'gifts' => 'array',
            'gifts.*.id' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:orderGifts,id',
            'gifts.*.realQty' => 'required|integer|min:0|max:' . DbHelper::MAX_INT,
            'gifts.*.realTotalBonuses' => 'required|integer|min:0|max:' . DbHelper::MAX_INT,
        ];
    }
}
