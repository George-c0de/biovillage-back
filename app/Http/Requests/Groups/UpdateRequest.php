<?php namespace App\Http\Requests\Groups;

use App\Helpers\DbHelper;

class UpdateRequest extends ShowOrDestroyRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'string|max:255',
            'bgColor' => 'string|min:1|max:10|regex:/^#\w{3,10}$/',
            'active' => 'boolean',
            'order' => 'integer|min:0|max:10000',
            'image' => 'image|mimes:jpeg,png,jpg|max:5000'
        ]);
    }
}
