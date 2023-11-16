<?php

namespace App\Http\Requests\Gift;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\ProductModel;
use Illuminate\Validation\Rule;

class IndexRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $parentRules = parent::rules();
        return array_merge(
            $parentRules,
            [
                'onlyActive' => 'boolean',
                'sort' => [
                    Rule::in(ProductModel::SORT_KEYS)
                ]
            ]
        );
    }
}
