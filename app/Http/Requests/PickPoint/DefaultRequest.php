<?php namespace App\Http\Requests\PickPoint;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Support\Facades\Validator;

class DefaultRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'address' => 'required|string|min:1|max:255',
            'lat' => [
                'required',
                'string',
                'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/',
            ],
            'lon' => [
                'required',
                'string',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            ],
            'contacts' => 'required|string|min:1|max:255',
            'isActive' => 'required|boolean',
            'workDays' => 'required|json'

        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            if($validator->errors()->count() > 0) {
                return;
            }

            $workDaysValidator = $this->validateWorkDays();

            if (!$workDaysValidator) {
                $validator->errors()->add('workDays',  trans('errors.workDays must be valid JSON') );
                return;
            }



            if ($workDaysValidator->errors()->count() > 0) {


                foreach($workDaysValidator->errors()->getMessages() as $error) {

                    $validator->errors()->add('workDays', $error );
                }
                return;
            }

        });
    }

    public function validateWorkDays() {

        $data = json_decode($this->workDays, true);

        if (!is_array($data))
            return false;

        $data = [ 'data' => $data ];

        $validator = Validator::make($data, [
            'data.*.name' => 'required|string|min:1|max:20',
            'data.*.isWork' => 'required|boolean',
            'data.*.workFrom' => 'required|string|max:5',
            'data.*.workTo' => 'required|string|max:5',
        ]);

        return $validator;

    }

}
