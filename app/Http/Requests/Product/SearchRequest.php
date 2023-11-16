<?php namespace App\Http\Requests\Product;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\ProductModel;
use App\Searchers\ProductSearcher;
use Illuminate\Validation\Rule;

class SearchRequest extends BaseApiRequest
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
                'groupId' => [
                    'integer',
                    'min:1',
                    'max:' . DbHelper::MAX_INT,
                    'exists:groups,id,deletedAt,NULL'
                ],
                'name' => 'required_if:sort,'.ProductModel::SORT_SIM.'|string|max:255',
                'onlyActive' => 'boolean',
                'onlyPromotion' => 'boolean',
                'tags' => 'array',
                'id' => 'integer|min:1|max:' . DbHelper::MAX_INT . '|exists:products,id,deletedAt,NULL',
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
