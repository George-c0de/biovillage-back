<?php namespace App\Http\Requests\Client;

use App\Http\Requests\BaseApiRequest;
use App\Models\Auth\Client;
use App\Service\ClientService;
use Illuminate\Validation\Rule;

class UpdateRequest extends ShowRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::validationRules(),
            [
                'name' => 'nullable|string|min:3|max:255',
                'birthday' => 'nullable|date_format:' . locale()->dateFormat,
                'allowMailing' => 'nullable|boolean',
                'email' => 'nullable|email|string|max:255',
                'avatar' => 'image|mimes:jpeg,png,jpg|max:5000',
            ]
        );
    }
}
