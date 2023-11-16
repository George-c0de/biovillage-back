<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class LoginRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ];
    }
}
