<?php

namespace Packages\Store\Http\Requests\Store;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use Packages\Store\Models\StoreModel;

class UpdateRequest extends BaseApiRequest
{

    public function rules()
    {
        return [
            'id' => sprintf(
                'bail|required|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new StoreModel)->getTable()
            ),
            'name' => 'nullable|string|min:3|max:255',
            'order' => 'nullable|integer|min:1|max:'.DbHelper::MAX_INT,
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'id' => $this['id'],
        ]);
    }
}
