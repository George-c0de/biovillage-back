<?php namespace App\Http\Requests\Unit;

use App\Helpers\DbHelper;
use App\Http\Requests\CheckIdRequest;

class UpdateRequest extends StoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'id' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:units,id,deletedAt,NULL',
            ]
        );
    }
}
