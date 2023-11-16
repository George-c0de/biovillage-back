<?php

namespace App\Http\Requests\Component;

use App\Http\Requests\BaseApiRequest;
use App\Models\ComponentModel;

class ComponentCollectionUpdateRequest extends BaseApiRequest {
    const RULES = [
        ComponentModel::SOCIALS => [
            'type' => 'nullable',
            'socialId' => 'nullable'
        ],
        ComponentModel::SLIDER_ON_INDEX => [
            'image' => 'nullable',
            'btnLink' => 'nullable'
        ],
        ComponentModel::OUR_SPECIALISTS => [
            'image' => 'nullable',
            'name' => 'nullable',
            'content' => 'nullable',
        ],
    ];

    public function rules() {
        $request = app()->request;
        $parameters = $request->route()->parameters();
        $slug = $parameters['slug'];

        return self::RULES[$slug];
    }
}
