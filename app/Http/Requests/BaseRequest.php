<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use App\Models\BotModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseRequest extends FormRequest {

    /**
     * Saved validated data
     * @var $validated
     */
    protected $validated;

    /**
     * Base validation rules
     * @param array $addRules - Addition or modifires rules
     * @return array
     */
    public static function validationRules($addRules = []) {
        $baseRules = [
            'sortDirect' => 'in:asc,desc'
        ];
        foreach($addRules as $k => $r) {
            if(is_array($r)) {
                $baseRules[$k] = array_merge($r, $baseRules[$k]);
            } else {
                $baseRules[$k] = implode( '|', [
                    $r,
                    $baseRules[$k] ?? ''
                ]);
            }
        }
        return $baseRules;
    }

    /**
     * Response as JSON
     * @var bool
     */
    protected $jsonError = false;

    /**
     * False
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules() {
        return self::validationRules();
    }

    /**
     * Return error response in case validation errors
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator) {
        if ($this->jsonError) {
            throw new HttpResponseException(
                ResponseHelper::error((new ValidationException($validator))->errors())
            );
        } else {
            parent::failedValidation($validator);
        }
    }

    /**
     * Get validated attribute
     * @param $attribute
     * @param null $default
     * @return null
     */
    public function validatedAttribute($attribute, $default = null)
    {
        if(empty($this->validated)) {
            $this->validated = $this->validated();
        }
        return $this->validated[$attribute] ?? $default;
    }

}
