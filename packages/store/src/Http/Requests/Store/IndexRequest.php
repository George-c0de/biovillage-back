<?php

namespace Packages\Store\Http\Requests\Store;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;
use Packages\Store\Models\StoreModel;

class IndexRequest extends BaseApiRequest
{

    public function rules()
    {
        return [
            'type' => [
                'nullable',
                Rule::in(StoreModel::TYPES),
            ],
        ];
    }
}
