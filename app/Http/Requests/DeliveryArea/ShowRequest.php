<?php namespace App\Http\Requests\DeliveryArea;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Http\Requests\CheckIdRequest;

class ShowRequest extends CheckIdRequest
{

    /**
     * Return base rules for validation
     * @return array
     */
    public static function getBaseRules() {
        return [
            'id' => 'required|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:deliveryArea,id,deletedAt,NULL',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return static::getBaseRules();
    }
}
