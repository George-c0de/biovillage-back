<?php namespace App\Http\Requests\Product;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\ProductModel;
use App\Searchers\ProductSearcher;
use Illuminate\Validation\Rule;

class SearchPromoRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $parentRules = parent::rules();
        return array_merge(
            $parentRules,
            [
                'perPage' => 'integer|min:1|max:' . DbHelper::MAX_INT,
                'page' => 'integer|min:1|max:' . DbHelper::MAX_INT,
                'name' => 'string|max:255',
                'tags' => 'array',
                'tags.*' => [
                    'integer',
                    'min:0',
                    'max:' . DbHelper::MAX_INT,
                    'exists:tags,id,deletedAt,NULL'
                ],
                'sort' => [
                    Rule::in(ProductModel::SORT_KEYS)
                ]
            ]
        );
    }
}
