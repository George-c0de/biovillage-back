<?php namespace App\Http\Requests\Slider;

use App\Helpers\DbHelper;
use App\Http\Requests\CheckIdRequest;

class ShowOrDestroyRequest extends CheckIdRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:sliders,id'
        ];
    }
}
