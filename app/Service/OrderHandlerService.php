<?php namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Notify;
use App\Helpers\Utils;
use App\Mail\OrderMail;
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
 * Handle order statuses
 *
 * @package App\Service\Section
 */
class OrderHandlerService extends BaseService
{


    /**
     * Send SMS about start delivery
     * Start delivery event
     * @param OrderModel $order
     */
    private function onStartDelivery(OrderModel $order) {
        Notify::sendSms(
            $order->client()->phone,
            'sms.start-delivery'
        );
    }

    /**
     * Main handler
     * @param $order
     * @param $newStatus
     */
    public function handle(OrderModel $order, $newStatus) {
        if ($newStatus == OM::STATUS_DELIVERY) {
            $this->onStartDelivery($order);
        }
    }

    /**
     * New order is placed
     * @param OrderModel $order
     */
    public function onNewOrder(OrderModel $order) {
        Notify::sendMail(env('ORDERS_EMAIL'),OrderMail::class, ['orderId' => $order->id]);
    }
}
