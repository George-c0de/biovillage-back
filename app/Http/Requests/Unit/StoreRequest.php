<?php namespace App\Http\Requests\Unit;

use App\Http\Requests\BaseApiRequest;
use App\Http\Requests\CheckIdRequest;

class StoreRequest extends CheckIdRequest
{

    /**
     * Base rules
     * @param array $addRules
     * @return array
     */
    public static function validationRules($addRules = [])
    {
        return array_merge(
            parent::validationRules($addRules),
            [
                'fullName' => 'required|string|min:1|max:255',
                'shortName' => 'required|string|min:1|max:20',
                'shortDerName' => 'required|string|min:1|max:20',
                'step' => 'required|integer|min:1|max:10000',
                'factor' => 'required|integer|min:1|max:10000',
            ]
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return static::validationRules();
    }

    /**
     * Validation rules
     * @param $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if($validator->errors()->count() > 0) {
                return;
            }
        });
    }
}
