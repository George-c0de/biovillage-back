<?php

namespace App\Http\Requests\Component;

use App\Http\Requests\BaseApiRequest;

class ComponentShowRequest extends BaseApiRequest {
    public function rules() {
        return [
            'slug' => 'required',
        ];
    }
}
