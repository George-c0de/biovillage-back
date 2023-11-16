<?php namespace App\Http\Requests\Billing;

use App\Http\Requests\BaseApiRequest;
use App\Models\Auth\Client;
use App\Service\BillingService;
use App\Service\ClientService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PaymentListenerRequest extends BaseApiRequest
{

    /**
     * Save notify request
     */
    protected function prepareForValidation()
    {
        parent::prepareForValidation();
        Log::debug('Gateway notify', [$this->getContent()]);
    }

    /**
     * Validation error
     * @param Validator $validator
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        if($validator->errors()->count() > 0) {
            Log::debug('Gateway notify errors', $validator->errors()->all());
        }
        parent::failedValidation($validator);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return resolve('Billing')->gateway()->getNotificationValidator()->rules();
    }

    /**
     * Addition validation
     * @param $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if($validator->errors()->count() > 0) {
                return;
            }
            //
            $data = $this->all();
            $data['ip'] = $this->ip();
            resolve('Billing')->gateway()->getNotificationValidator()->validate(
                $data,
                $validator->errors()
            );
        });
    }
}
