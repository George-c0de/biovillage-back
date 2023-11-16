<?php

namespace App\Http\Requests\ComponentCollection;

use App\Http\Requests\BaseApiRequest;
use App\Models\ComponentModel;

class ComponentCollectionStoreRequest extends BaseApiRequest {
    protected $rules = [
        ComponentModel::SOCIALS => [
            'type' => 'required',
            'socialId' => 'required',
        ],
        ComponentModel::SLIDER_ON_INDEX => [
            'image' => 'required',
            'btnLink' => 'required'
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
