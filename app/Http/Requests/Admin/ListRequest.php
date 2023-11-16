<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;
use App\Http\Requests\BaseRequest;
use App\Service\AdminService;
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
        $baseRules = BaseRequest::validationRules();
        return array_merge(
            $baseRules,
            [
                // sortDirect in base validation rules
                'sort' => [ Rule::in(AdminService::SORTS_FIELDS) ]
            ]
        );
    }
}
