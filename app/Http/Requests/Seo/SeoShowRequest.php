<?php

namespace App\Http\Requests\Seo;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;

class SeoShowRequest extends BaseApiRequest {
    public function rules() {
        return [
            'entityId' => 'required|integer|min:0|max:'.DbHelper::MAX_INT,
            'groupName' => 'required',
        ];
    }
}
