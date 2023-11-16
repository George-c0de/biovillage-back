<?php namespace App\Http\Requests\Slider;

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
            'id' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:sliders,id',
            'name' => 'string|min:1|max:255',
            'description' => 'string|min:1|max:255',
            'bgColor' => 'string|min:3|max:10|regex:/^#\w{3,10}$/',
            'order' => 'integer|min:0|max:'. DbHelper::MAX_INT,
            'active' => 'boolean',
            'image' => 'image|mimes:jpeg,png,jpg|max:5000',
        ];
    }
}
