<?php

namespace Packages\Store\Http\Requests\Store;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;
use Packages\Store\Models\StoreModel;

class StoreRequest extends BaseApiRequest
{

    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'type' => [
                'nullable',
                Rule::in(StoreModel::TYPES),
            ],
            'order' => 'nullable|integer|min:1|max:'.DbHelper::MAX_INT,
        ];
    }
}
