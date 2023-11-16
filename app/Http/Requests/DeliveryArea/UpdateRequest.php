<?php namespace App\Http\Requests\DeliveryArea;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Http\Requests\CheckIdRequest;

class UpdateRequest extends ShowRequest
{

    /**
     * Return base rules for validation
     * @return array
     */
    public static function getBaseRules() {
        $rules = parent::getBaseRules();
        return array_merge($rules, [
            'active' => 'boolean',
            'name' => 'string|min:3|max:255',
            'price' => 'int|max:' . DbHelper::MAX_INT,
            'color' => [ 'string', 'regex:/^#[a-fA-F0-9]{6}$/' ],
            'deliveryFreeSum' => 'int|min:0|max:' . DbHelper::MAX_INT,
        ]);
    }
}
