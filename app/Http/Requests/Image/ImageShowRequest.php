<?php

namespace App\Http\Requests\Image;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;

class ImageShowRequest extends BaseApiRequest {
    public function rules() {
        return [
            'groupName' => 'required',
            'entityId' => 'required|integer|min:0|max:'.DbHelper::MAX_INT,
        ];
    }
}
