<?php namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\OrderGiftModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel as OM;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Searchers\OrderGiftsSearcher;
use App\Searchers\OrderItemsSearcher;
use App\Searchers\OrderPaymentsSearcher;
use App\Searchers\OrderSearcher;
use App\Searchers\ProductSearcher;
use App\Service\Images\ImageService;
use Faker\Provider\Payment;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Order status validator
 *
 * @package App\Service\Section
 */
class OrderStatusNavigator
{


    /**
     * Statuses navigation map
     */
    const STATUSES_NAVIGATION = [
        OM::STATUS_NEW => [ OM::STATUS_PLACED, OM::STATUS_CANCEL ],
        OM::STATUS_PLACED => [ OM::STATUS_CANCEL, OM::STATUS_FINISHED, OM::STATUS_PACKED ],
        OM::STATUS_PACKED => [ OM::STATUS_CANCEL, OM::STATUS_FINISHED, OM::STATUS_DELIVERY],
        OM::STATUS_DELIVERY => [ OM::STATUS_CANCEL, OM::STATUS_FINISHED ],
        OM::STATUS_FINISHED => [ OM::STATUS_CANCEL ]
    ];

    /**
     * Allow change or not
     * @param $status
     * @param $newStatus
     * @param bool $allowDuplicate
     * @return bool
     */
    public static function canNavigate($status, $newStatus, $allowDuplicate = false) {
        if($allowDuplicate && $status == $newStatus) {
            return true;
        }
        return in_array($newStatus, static::STATUSES_NAVIGATION[$status] ?? []);
    }

    /**
     * Determine next order status if payment received
     * @param $order
     * @return string
     */
    public static function nextOrderStatusIfPaymentReceived($order) {

        if(in_array($order->primaryPaymentMethod , [
            PaymentModel::METHOD_COURIER_CARD,
            PaymentModel::METHOD_CASH])) {
            return OrderModel::STATUS_FINISHED;
        } else {
            return OrderModel::STATUS_PLACED;
        }
    }
}
