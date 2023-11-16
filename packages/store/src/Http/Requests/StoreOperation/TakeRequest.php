<?php

namespace Packages\Store\Http\Requests\StoreOperation;


class TakeRequest extends StoreRequest
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            'force' => 'nullable|boolean',
        ]);
    }
}
