<?php

namespace App\Http\Requests\Order;

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

class UpdateRequest extends OrderIdRequest {

    public function rules() {
        return array_merge( parent::rules(), [
            'deliveryIntervalId' => 'integer|min:1|max:' . DbHelper::MAX_INT . '|exists:deliveryIntervals,id,deletedAt,NULL',
            'status' => [Rule::in(OrderModel::STATUES)],
            'adminsComment' => 'nullable|string|max:5000',
            'commentForClient' => 'nullable|string|max:5000',
            'deliveryDate' => 'nullable|date_format:' . locale()->dateFormat,
        ]);
    }
}
