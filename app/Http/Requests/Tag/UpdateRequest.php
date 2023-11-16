<?php namespace App\Http\Requests\Tag;

use App\Helpers\DbHelper;
use App\Http\Requests\CheckIdRequest;

class UpdateRequest extends CheckIdRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:tags,id,deletedAt,NULL',
            'name' => 'string|min:3|max:255',
            'order' => 'integer|min:0|max:'.DbHelper::MAX_INT,
            'active' => 'boolean',
        ];
    }
}
