<?php namespace App\Http\Requests\DeliveryIntervals;

use App\Helpers\DbHelper;
use App\Http\Requests\CheckIdRequest;

class UpdateRequest extends ShowOrDestroyRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        return array_merge(
            $rules,
            StoreRequest::getBaseRules()
        );
    }
}
