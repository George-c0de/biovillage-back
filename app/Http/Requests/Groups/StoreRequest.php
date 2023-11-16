<?php namespace App\Http\Requests\Groups;

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
            'active' => 'required|boolean',
            'name' => 'required|string|required|max:255',
            'bgColor' => 'required|string|min:1|max:10|regex:/^#\w{3,10}$/',
            'order' => 'required|integer|min:0|max:10000',
            'image' => 'image|mimes:jpeg,png,jpg|max:5000',
        ];
    }
}
