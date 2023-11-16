<?php

namespace App\Http\Requests\Queue;

use App\Http\Requests\BaseRequest;
use App\Models\FailedJobModel;

class GetFailedJobRequest extends BaseRequest
{

    public static function validationRules() {
        return [
            'id' => sprintf(
                'required|exists:%s,id',
                with(new FailedJobModel())->getTable()
            )
        ];
    }

    public function rules()
    {
        return self::validationRules();
    }
}