<?php

namespace App\Http\Requests\Image;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;

class ImageDeleteRequest extends BaseApiRequest {
    public function rules() {
        return [
            'ids' => 'nullable|array',
        ];
    }
}
