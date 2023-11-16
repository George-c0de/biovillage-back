<?php

namespace Packages\Store\Http\Requests\StoreGiftOperation;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\ProductModel;

class ProductResidueRequest extends BaseApiRequest
{
    public function rules()
    {
        return array_merge(parent::rules(),[
            'id' => sprintf(
                'bail|required|integer|exists:%s,id|min:1|max:'.DbHelper::MAX_INT,
                with(new ProductModel())->getTable()
            ),
        ]);
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'id' => $this['id'],
        ]);
    }

}
