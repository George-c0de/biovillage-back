<?php

namespace Packages\Store\Http\Requests\StorePlace;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use Packages\Store\Models\StorePlaceModel;

class DeleteRequest extends BaseApiRequest
{

    public function rules()
    {
        return [
            'id' => sprintf(
                'bail|required|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new StorePlaceModel)->getTable()
            ),
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'id' => $this['id'],
        ]);
    }
}
