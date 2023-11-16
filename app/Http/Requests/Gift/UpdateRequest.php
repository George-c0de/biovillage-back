<?php

namespace App\Http\Requests\Gift;

use App\Helpers\DbHelper;
use App\Http\Requests\CheckIdRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends CheckIdRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = StoreRequest::getRules();
        $rules['id'] = 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:gifts,id,deletedAt,NULL';
        return $rules;
    }
}
