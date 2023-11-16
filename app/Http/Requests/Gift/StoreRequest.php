<?php

namespace App\Http\Requests\Gift;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\ProductModel;
use Illuminate\Validation\Rule;

class StoreRequest extends BaseApiRequest
{

    /**
     * Validation rules
     * @param array $addRules - Addition rules. Support only strings
     * @return array
     */
    public static function getRules($addRules = []) {
        $baseRules = [
            'order' => 'integer|min:0|max:'. DbHelper::MAX_INT,
            'active' => 'boolean',
            'name' => 'string|min:1|max:255',
            'description' => 'string|min:1|max:5000',
            'price' => 'integer|min:1|max:' . DbHelper::MAX_INT,
            'image' => 'image|mimes:jpeg,png,jpg|max:5000',
            'composition' => 'string|max:255',
            'shelfLife' => 'string|max:128'
        ];
        foreach($addRules as $k => $r) {
            $baseRules[$k] = implode( '|', [
                $addRules[$k],
                $baseRules[$k] ?? []
            ]);
        }
        return $baseRules;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return static::getRules([
            'name' => 'required',
            'price' => 'required',
        ]);
    }
}
