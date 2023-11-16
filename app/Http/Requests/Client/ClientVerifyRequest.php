<?php namespace App\Http\Requests\Client;

use App\Http\Requests\BaseApiRequest;
use App\Models\Auth\Client;
use App\Service\ClientService;
use Illuminate\Validation\Rule;

class ClientVerifyRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone' => 'required|string|min:10|max:20',
            'code' => 'required|string|size:5',
            'platform' => [
                'string',
                Rule::in(Client::PLATFORMS)
            ]
        ];
    }
}
