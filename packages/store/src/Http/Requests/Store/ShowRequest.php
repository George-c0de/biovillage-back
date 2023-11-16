<?php

namespace Packages\Store\Http\Requests\Store;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use Packages\Store\Models\StoreModel;

class ShowRequest extends BaseApiRequest
{

    public function rules()
    {
        return [
            'id' => sprintf(
                'bail|required|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new StoreModel)->getTable()
            ),
        ];
    }

    protected function prepareForValidation() {
        $this->merge([
            'id' => $this['id'],
        ]);
    }
}
