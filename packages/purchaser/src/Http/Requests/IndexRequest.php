<?php

namespace Packages\Purchaser\Http\Requests;

use App\Http\Requests\BaseApiRequest;
use App\Models\OrderModel;
use Illuminate\Validation\Rule;

class IndexRequest extends BaseApiRequest {

    public function rules() {

        return array_merge( parent::rules(),  [
            'date' => 'required|date_format:' . locale()->dateFormat,
            'startHour' => 'nullable|int|min:0|max:24',
            'endHour' => 'nullable|int|min:0|max:24',
            'orderStatus' => [ Rule::in(OrderModel::STATUES) ],
        ]);
    }
}
