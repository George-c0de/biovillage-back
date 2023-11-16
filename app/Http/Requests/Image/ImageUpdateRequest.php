<?php

namespace App\Http\Requests\Image;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;

class ImageUpdateRequest extends BaseApiRequest {
    public function rules() {
        return [
            'data' => 'required|array',
        ];
    }
}
