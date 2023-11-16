<?php

namespace App\Http\Requests\Component;

use App\Http\Requests\BaseApiRequest;
use App\Models\ComponentModel;
use Illuminate\Support\Arr;

class ComponentUpdateRequest extends BaseApiRequest {
    const RULES_MAIN = [
        'name' => 'nullable',
        'caption' => 'nullable',
        'title' => 'nullable',
        'content' => 'nullable',
    ];

    const RULES_PROPERTIES = [
        ComponentModel::MAIN_SETTINGS => [
            'properties' => 'nullable|array',
            'properties.email' => 'nullable|email',
            'properties.phone' => 'nullable',
            'properties.address' => 'nullable',
            'properties.mapLink' => 'nullable',
            'properties.phoneHotline' => 'nullable',
            'properties.operationMode' => 'nullable',
        ],
    ];

    public function rules() {
        $request = app()->request;
        $parameters = $request->route()->parameters();
        $slug = $parameters['slug'];

        $rules = self::RULES_MAIN;

        if (Arr::has(self::RULES_PROPERTIES, $slug)) {
            $rules = array_merge($rules, self::RULES_PROPERTIES[$slug]);
        }

        return $rules;
    }
}
