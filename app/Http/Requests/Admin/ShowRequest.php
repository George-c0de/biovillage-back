<?php

namespace App\Http\Requests\Admin;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\Auth\Admin;

class ShowRequest extends BaseApiRequest
{
    public function rules()
    {
        return [
            'id' => sprintf(
                'required|integer|exists:%s,id,deletedAt,NULL|min:1|max:'.DbHelper::MAX_INT,
                with(new Admin())->getTable()
            ),
        ];
    }

    /**
     * Перед валидацией подставляем занчения
     */
    protected function prepareForValidation() {
        $this->merge([
            'id' => $this['id']
        ]);
    }
}
