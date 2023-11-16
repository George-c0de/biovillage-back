<?php

namespace Packages\Store\Http\Requests\StoreOperation;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use Packages\Store\Models\StoreOperationModel;

class ShowRequest extends BaseApiRequest
{
    public function rules()
    {
        return array_merge(parent::rules(),[
            'id' => sprintf(
                'bail|required|integer|exists:%s,id|min:1|max:'.DbHelper::MAX_INT,
                with(new StoreOperationModel())->getTable()
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
