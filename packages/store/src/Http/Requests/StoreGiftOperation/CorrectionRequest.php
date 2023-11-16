<?php

namespace Packages\Store\Http\Requests\StoreGiftOperation;

use App\Helpers\DbHelper;
use Packages\Store\Models\StoreModel;

class CorrectionRequest extends StoreRequest
{
    public function rules()
    {
        return parent::rules();
    }
}
