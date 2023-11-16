<?php

namespace App\Http\Requests\PickPoint;

use App\Helpers\DbHelper;
use App\Http\Requests\BaseApiRequest;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\SettingsModel;
use App\Service\AddressService;
use App\Service\ProductService;
use App\Service\SettingsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateRequest extends DefaultRequest {


}
