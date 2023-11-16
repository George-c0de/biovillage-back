<?php namespace App\Http\Requests\Slider;

use App\Http\Requests\BaseApiRequest;

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
            'description' => 'required|string|min:1|max:255',
            'bgColor' => 'required|string|min:1|max:10|regex:/^#\w{3,10}$/',
            'active' => 'required|boolean',
            'order' => 'required|integer|min:0|max:1000',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5000',
        ];
    }
}
