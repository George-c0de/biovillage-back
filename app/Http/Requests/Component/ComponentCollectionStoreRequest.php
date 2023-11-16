<?php

namespace App\Http\Requests\Component;

use App\Http\Requests\BaseApiRequest;
use App\Models\ComponentModel;

class ComponentCollectionStoreRequest extends BaseApiRequest {
    const RULES = [
        ComponentModel::SOCIALS => [
            'type' => 'required',
            'socialId' => 'required',
        ],
        ComponentModel::SLIDER_ON_INDEX => [
            'image' => 'required',
            'btnLink' => 'required'
        ],
        ComponentModel::OUR_SPECIALISTS => [
            'image' => 'required',
            'name' => 'required',
            'content' => 'required',
        ],
    ];

    public function rules() {
        $request = app()->request;
        $parameters = $request->route()->parameters();
        $slug = $parameters['slug'];

        return self::RULES[$slug];
    }
}
