<?php namespace App\Http\Requests\Client;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\Auth\Client;
use App\Service\ClientService;

class BonusesRequest extends ShowRequest
{

    /**
     * Rules
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'bonuses' => 'int|min:0|max:1000000'
            ]
        );
    }
}
