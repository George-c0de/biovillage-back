<?php

namespace App\Http\Requests\Queue;

use App\Http\Requests\BaseRequest;
use App\Models\FailedJobModel;

class ActionFailedJobRequest extends BaseRequest
{

    public static function validationRules() {
        $idRule = GetFailedJobRequest::validationRules()['id'];
        return array_merge(
            [
                'ids' => 'required|array',
                'ids.*' => $idRule,
                'action' => 'required|in:delete,retry'
            ]
        );
    }

    public function rules()
    {
        return self::validationRules();
    }
}