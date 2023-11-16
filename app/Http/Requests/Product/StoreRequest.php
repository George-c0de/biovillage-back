<?php namespace App\Http\Requests\Product;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Http\Requests\CheckIdRequest;
use App\Models\ProductModel;
use App\Models\UnitModel;
use Illuminate\Validation\Rule;

class StoreRequest extends CheckIdRequest
{

    /**
     * Validation rules
     * @param array $addRules
     * @return array
     */
    public static function validationRules($addRules = []) {
        return [
            'order' => 'integer|min:0|max:'. DbHelper::MAX_INT,
            'active' => 'boolean',
            'name' => 'required|string|min:1|max:255',
            'description' => 'string|min:1|max:5000',
            'unitId' => [
                'required',
                'integer', 'min:1', 'max:' . DbHelper::MAX_INT,
                'exists:units,id,deletedAt,NULL',
            ],
            'unitStep' => 'integer|min:1|max:10000',
            'price' => 'required|integer|min:1|max:' . DbHelper::MAX_INT,
            'bonusesPercentage' => 'integer|min:0|max:500',
            'promotion' => [
                Rule::in(ProductModel::PROMOTIONS)
            ],
            'groupId' => [
                'required',
                'integer', 'min:1', 'max:'.DbHelper::MAX_INT,
                'exists:groups,id,deletedAt,NULL'
            ],
            'image' => 'image|mimes:jpeg,png,jpg|max:5000',
            'tags' => 'array',
            'tags.*' => 'integer|min:1|max:' . DbHelper::MAX_INT . '|exists:tags,id,deletedAt,NULL',
            'composition' => 'nullable|string|max:2000',
            'shelfLife' => 'nullable|string|max:2000',
            'nutrition' => 'nullable|string|max:2000',
            'netCostPerStep' => 'nullable|integer|min:0|max:'.DbHelper::MAX_INT,
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return static::validationRules();
    }


    /**
     * Validation rules
     * @param $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if($validator->errors()->count() > 0) {
                return;
            }
        });
    }
}
