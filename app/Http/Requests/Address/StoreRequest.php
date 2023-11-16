<?php namespace App\Http\Requests\Address;

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
            'city' => 'required|string|min:1|max:255',
            'street' => 'required|string|min:1|max:255',
            'house' => 'nullable|string|min:1|max:64',
            'building' => 'nullable|string|min:1|max:255',
            'entrance' => 'nullable|string|min:1|max:64',
            'floor' => 'nullable|string|min:0|max:32',
            'doorphone' => 'nullable|string|min:1|max:255',
            'appt' => 'nullable|string|min:1|max:1000',
            'lat' => [
                'required',
                'string',
                'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/',
            ],
            'lon' => [
                'required',
                'string',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            ],
            'comment' => 'nullable|string|min:1|max:3000',
        ];
    }
}
