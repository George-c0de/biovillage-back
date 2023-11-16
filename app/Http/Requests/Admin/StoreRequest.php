<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;
use App\Service\AdminRole;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseApiRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|min:2|max:255',
            'phone' => 'required|string|min:10|max:20|unique:admins,phone',
            'password' => 'required|string|min:2|max:255',
            'roles' => 'required|array',
            'roles.*' => [
                'required',
                Rule::in(AdminRole::ROLES),
            ],
        ];
    }
}
