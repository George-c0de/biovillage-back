<?php namespace App\Http\Requests\Client;

use App\Http\Requests\BaseApiRequest;

class ClientRegisterRequest extends BaseApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => 'required|string|min:10|max:20',
            'referral' => 'string|min:5|max:12',
        ];
    }
}
