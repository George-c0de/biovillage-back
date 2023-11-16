<?php namespace App\Http\Requests\Client;

use App\Http\Requests\BaseApiRequest;
use App\Models\Auth\Client;
use App\Service\ClientService;
use Illuminate\Validation\Rule;

class UpdateBirthdayRequest extends ShowRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'birthday' => 'nullable|date_format:' . locale()->dateFormat,
        ];
    }
}
