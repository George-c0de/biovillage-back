<?php namespace App\Http\Requests\Tag;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Http\Requests\CheckIdRequest;

class StoreRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'order' => 'required|integer|min:0|max:'.DbHelper::MAX_INT,
            'active' => 'required|boolean',
        ];
    }
}
