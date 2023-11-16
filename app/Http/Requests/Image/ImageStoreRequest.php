<?php

namespace App\Http\Requests\Image;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;

class ImageStoreRequest extends BaseApiRequest {
    public function rules() {
        return [
            'images' => 'nullable|array',
            'images.*' => 'nullable|image',
        ];
    }
}
