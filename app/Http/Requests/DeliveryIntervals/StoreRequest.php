<?php namespace App\Http\Requests\DeliveryIntervals;

use App\Http\Requests\BaseApiRequest;

class StoreRequest extends BaseApiRequest
{

    /**
     * Return base rules for validation
     * @return array
     */
    public static function getBaseRules() {
        return [
            'active' => 'required|boolean',
            'dayOfWeek' => 'required|integer|min:1|max:7',
            'startHour' => 'required|integer|min:0|max:24',
            'startMinute' => 'required|integer|min:0|max:60',
            'endHour' => 'required|integer|min:0|max:24',
            'endMinute' => 'required|integer|min:0|max:60',
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
