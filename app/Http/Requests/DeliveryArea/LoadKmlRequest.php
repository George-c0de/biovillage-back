<?php namespace App\Http\Requests\DeliveryArea;

use App\Http\Requests\BaseApiRequest;

class LoadKmlRequest extends BaseApiRequest
{

    /**
     * Return base rules for validation
     * @return array
     */
    public static function getBaseRules() {
        return [
            'kmlFile' => 'required|file|max:5000'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return self::getBaseRules();
    }
}
