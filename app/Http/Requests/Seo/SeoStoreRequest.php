<?php

namespace App\Http\Requests\Seo;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;

class SeoStoreRequest extends BaseApiRequest {
    public function rules() {
        return [
            'seoTitle' => 'nullable',
            'seoDescription' => 'nullable',
            'ogLocale' => 'nullable',
            'ogType' => 'nullable',
            'ogSiteName' => 'nullable',
            'ogTitle' => 'nullable',
            'ogDescription' => 'nullable',
            'ogUrl' => 'nullable',
            'ogImage' => 'nullable',
            'ogVideo' => 'nullable',
        ];
    }
}
