<?php namespace App\Http\Requests\Client;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\Auth\Client;
use App\Service\ClientService;
use Illuminate\Validation\Rule;

class ListRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return array_merge(
            parent::validationRules(),
            [
                'id' => 'integer|min:1|max:' . DbHelper::MAX_INT,
                'perPage' => 'integer|min:1|max:1000',
                'page' => 'integer|min:1|max:' . DbHelper::MAX_INT,
                'name' => 'nullable|string|min:3|max:255',
                'phone' => 'nullable|string|min:3|max:100',
                'platform' => [
                    Rule::in(Client::PLATFORMS)
                ],
                'dtRegBegin' => 'nullable|date_format:' . locale()->dateFormat,
                'dtRegEnd' => 'nullable|date_format:' . locale()->dateFormat,
                'dtLastLoginBegin' => 'nullable|date_format:' . locale()->dateFormat,
                'dtLastLoginEnd' => 'nullable|date_format:' . locale()->dateFormat,
                'sort' => [ Rule::in(Client::SORT_KEYS) ]
            ]
        );
    }
}
