<?php

namespace Packages\Store\Http\Requests\Store;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;
use Packages\Store\Models\StoreModel;

class StoreTypeRequest extends BaseApiRequest
{

    public function rules()
    {
        return [
            'storeType' => [
                'required',
                Rule::in(StoreModel::TYPES),
            ],
        ];
    }
}
