<?php

namespace App\Http\Requests\ComponentCollection;

use App\Http\Requests\BaseApiRequest;
use App\Models\ComponentModel;

class ComponentCollectionUpdateRequest extends BaseApiRequest {
    protected $rules = [
        ComponentModel::SOCIALS => [
            'type' => 'nullable',
            'socialId' => 'nullable'
        ],
        ComponentModel::SLIDER_ON_INDEX => [
            'image' => 'nullable',
            'btnLink' => 'nullable'
        ],
    ];

    public function rules() {
        $request = app()->request;
        $parameters = $request->route()->parameters();
        $slug = $parameters['slug'];

        return array_merge(
            parent::rules(),
            $this->rules[$slug]
        );
    }
}
