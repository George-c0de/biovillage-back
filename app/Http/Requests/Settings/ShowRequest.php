<?php namespace App\Http\Requests\Settings;

use App\Http\Requests\BaseApiRequest;
use App\Models\SettingsModel;
use App\Service\SettingsService;
use Illuminate\Validation\Rule;

class ShowRequest extends BaseApiRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'key' => [
                'string',
                Rule::in(array_keys(SettingsModel::DEFAULT_SETTINGS))
            ]
        ];
    }
}
