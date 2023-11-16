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

class IndexRequest extends BaseApiRequest {

    public function rules() {

        return array_merge( parent::rules(),  [
            'perPage' => 'integer|min:1|max:1000',
            'page' => 'integer|min:1|max:' . DbHelper::MAX_INT,
            'dtCreatedBegin' => 'nullable|date_format:' . locale()->dateFormat,
            'dtCreatedEnd' => 'nullable|date_format:' . locale()->dateFormat,
            'dtCreated' => 'nullable|date_format:' . locale()->dateFormat,
            'dtFinishedBegin' => 'nullable|date_format:' . locale()->dateFormat,
            'dtFinishedEnd' => 'nullable|date_format:' . locale()->dateFormat,
            'dtFinished' => 'nullable|date_format:' . locale()->dateFormat,
            'dtDeliveryBegin' => 'nullable|date_format:' . locale()->dateFormat,
            'dtDeliveryEnd' => 'nullable|date_format:' . locale()->dateFormat,
            'dtDelivery' => 'nullable|date_format:' . locale()->dateFormat,
            'clientId' => 'nullable|integer|min:1|max:' . DbHelper::MAX_INT . '|exists:clients,id,deletedAt,NULL',
            'status' => [ Rule::in(OrderModel::STATUES) ],
            'sort' => [ Rule::in(OrderModel::SORTS) ],
            'dtPackedBegin' => 'nullable|date_format:' . locale()->dateFormat,
            'dtPackedEnd' => 'nullable|date_format:' . locale()->dateFormat,
            'dtPacked' => 'nullable|date_format:' . locale()->dateFormat,
            'deliveryHourBegin' => 'int|min:0|max:24',
            'deliveryHourEnd' => 'int|min:0|max:24',
            'clientPhone' => 'nullable|string|max:36',
            'id' => 'integer|min:1|max:' . DbHelper::MAX_INT . '|exists:orders,id',
            'showErrorsOrdersAlso' => 'boolean'
        ]);
    }
}
