<?php namespace App\Service;

use App\Helpers\DbHelper;
use App\Helpers\Utils;
use App\Models\OrderGiftModel;
use App\Models\OrderItemModel;
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
use Hamcrest\Util;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Order Service.
 *
 * @package App\Service\Section
 */
class OrderService
{

    /**
     * Recalc delivery date by delivery interval
     * @param $orderId
     */
    private static function recalcDeliveryIntervalDate($orderId) {
        Db::statement('
            UPDATE orders AS o
            SET "deliveryDate" = (CASE
              WHEN di."dayOfWeek" < extract(dow from now())::int
              THEN CURRENT_DATE + (7 - extract(dow from now())::int
                + di."dayOfWeek")::int * interval \'1 day\'
              ELSE CURRENT_DATE + ( di."dayOfWeek"
                - extract(dow from now())::int )::int * interval \'1 day\'
            END)::date
            FROM "deliveryIntervals" di
            WHERE o."deliveryIntervalId" = di.id AND o.id = ?
        ', [ $orderId ]);
    }

    /**
     * New order
     * @param $params
     * @return int
     */
    public static function addOrder($params) {

        // Add order
        $daId = AddressService::getDeliveryAreaId($params['addressId']);
        $order = DB::transaction(function() use($params, $daId) {

            $o = new OrderModel();
            $o->createdAt = DbHelper::currTs();
            $o->updatedAt = DbHelper::currTs();
            $o->status = static::getInitStatus($params);
            $o->clientId = Auth::id();
            $o->clientsComment = $params['clientsComment'] ?? '';
            $o->promoCode = $params['promoCode'] ?? '';
            $o->platform = Auth::user()->lastPlatform;
            $o->addressId = $params['addressId'];
            $o->deliveryIntervalId = $params['deliveryIntervalId'];
            $o->deliveryDate = Db::raw('NOW()'); // Update bottom
            $o->productsSum = $params['productsSum'];
            $o->deliverySum = $params['deliverySum'];
            $o->bonuses = $params['giftBonuses'] ?? 0;
            $o->total = $params['total'];
            $o->deliveryAreaId = $daId;
            $o->primaryPaymentMethod = $params['paymentPrimaryMethod'];
            $o->actionIfNotDelivery = $params['actionIfNotDelivery'] ??
                OrderModel::ACTION_FIND_ANALOG;
            $o->save();

            static::recalcDeliveryIntervalDate($o->id);

            foreach($params['products'] as $prod) {
                $i = new OrderItemModel();
                $i->createdAt = DbHelper::currTs();
                $i->orderId = $o->id;
                $i->productId = $prod['id'];
                $i->qty = $prod['qty'] ?? 1;
                $i->price = $prod['price'];
                $i->total = $prod['total'];
                $i->realUnits = 0;
                $i->realPrice = $i->price;
                $i->realTotal = 0;
                $i->save();
            }

            DB::statement('
                UPDATE "orderItems" AS oi
                SET "unitId" = p."unitId"
                FROM products p
                    INNER JOIN units u ON u.id = p."unitId"
                WHERE p.id = oi."productId" AND oi."orderId" = ?
            ', [ $o->id ]);

            if(!empty($params['gifts'])) {
                foreach($params['gifts'] as $gift) {
                    $g = new OrderGiftModel();
                    $g->createdAt = DbHelper::currTs();
                    $g->orderId = $o->id;
                    $g->giftId = $gift['id'];
                    $g->qty = $gift['qty'] ?? 1;
                    $g->bonuses = $gift['price'];
                    $g->totalBonuses = $gift['total'];
                    $g->save();
                }
            }

            return $o;
        });

        // Try to payment
        $params['orderId'] = $order->id;
        try {
            PaymentService::createPayment($params);
        } catch (\Exception $e) {
            $order->error = $e->getMessage();
            $order->save();
            throw $e;
        }

        // Notify about new order
        resolve('OrderStatusHandler')->onNewOrder($order);

        return $order;
    }

    /**
     * Determination initial order status
     * @param $params
     * @return string - OrderMode::STATUS_*
     */
    public static function getInitStatus($params) {
        $payType = $params['paymentPrimaryMethod'] ?? Utils::raise('No payment type');
        if(in_array($payType, [
            PaymentModel::METHOD_CASH, PaymentModel::METHOD_COURIER_CARD
        ])) {
            return OrderModel::STATUS_PLACED;
        }
        return OrderModel::STATUS_NEW;
    }

    /**
     * Search orders wide range conditions
     * @param array $params - See OrderSearcher
     * @return mixed
     */
    public static function search($params = []) {
        $s = new OrderSearcher();
        return $s->search($params);
    }

    /**
     * Search one order
     * @param array $params - See OrderSearcher
     * @return array|null
     */
    public static function searchOne($params = []) {
        $s = new OrderSearcher();
        $orders = $s->search($params);
        if(!empty($orders)) {
            return $orders[0];
        }
        return null;
    }

    /**
     * Search orders wide range conditions with detailed
     * @param array $params - See OrderSearcher
     * @return mixed
     */
    public static function searchDetailed($params = []) {
        $s = new OrderSearcher();
        return $s->searchDetailed($params);
    }

    /**
     * Search only one
     * @param $id
     * @param bool $showConfirmation
     * @return null|array
     */
    public static function searchDetailsOne($id, $showConfirmation = false) {
        $orders = static::searchDetailed([
            'id' => $id,
            'showPaymentConfirmation' => $showConfirmation
        ]);
        if(empty($orders)) {
            return null;
        }
        return $orders[0];
    }

    /**
     * Get orders count
     * @param array $params - See OrderSearcher
     * @return mixed
     */
    public static function getCount($params = []) {
        $s = new OrderSearcher();
        return $s->getCount($params);
    }

    /**
     * Get client order count
     * @param $client
     * @return mixed
     */
    public static function getClientCompletedOrdersCount($client) {
        return static::getCount([
            'clientId' => $client->id,
            'status' => OrderModel::STATUS_FINISHED
        ]);
    }

    /**
     * Update order info. Comments for clients, admin comment, status
     * @param $params
     * @return OrderModel
     */
    public static function updateOrder($params) {

        //
        Utils::nullToStrInArray(['adminsComment', 'commentForClient'], $params);
        $o = OrderModel::orderInstance($params['order'] ??
            $params['orderId'] ?? $params['id'] ?? -1);

        // New status
        $status = $params['status'] ?? '';
        if(!empty($status)) {
            static::_doChangeStatus($o,$status,true);
        }
        $o->updatedAt = DbHelper::currTs();
        $o->fill($params);
        $o->save();

        // Refresh delivery date
        if(!empty($params['deliveryIntervalId'])) {
            static::recalcDeliveryIntervalDate($o->id);
        }

        return $o;
    }


    /**
     * Show all order items
     * @param $orderId
     * @return mixed
     */
    public static function items($orderId) {
        $s = new OrderItemsSearcher();
        return $s->search(['orderId' => $orderId]);
    }

    /**
     * Show all payments for order
     * @param $orderId
     * @return mixed
     */
    public static function payments($orderId) {
        $s = new OrderPaymentsSearcher();
        return $s->search(['orderId' => $orderId]);
    }

    /**
     * Show all gifts
     * @param $orderId
     * @return
     */
    public static function gifts($orderId) {
        $s = new OrderGiftsSearcher();
        return $s->search(['orderId' => $orderId]);
    }

    /**
     * Only change order status. Not save order instance.
     * @param $order
     * @param $newStatus
     * @param bool $ignoreDuplicate
     */
    public static function _doChangeStatus($order, $newStatus, $ignoreDuplicate = false) {

        $order = OrderModel::orderInstance($order);

        if(!$ignoreDuplicate && $newStatus == $order->status) {
            Utils::raise('Same status', [
                'orderId' => $order->id,
                'newStatus' => $newStatus
            ]);
        }

        $order->updatedAt = DbHelper::currTs();
        if(OrderStatusNavigator::canNavigate($order->status, $newStatus)) {
            $order->status = $newStatus;
            $statusDateField = OrderModel::STATUS_DATES_MAP[$newStatus] ?? '';
            if(!empty($statusDateField)) {
                $order->$statusDateField = DbHelper::currTs();
            }

            // Handle changes
            resolve('OrderStatusHandler')->handle($order, $newStatus);

        } else {
            $order->error .= locale()->dateTimeToDbStr(now());
            $order->error .= trans('validation.badStatusToTransfer', [
                'currentStatus' => $order->status,
                'newStatus' => $newStatus
            ]);
            $order->error .= "\n";
        }
    }

    /**
     * Change order status
     * @param $order
     * @param $newStatus
     * @param $ignoreDuplicate
     */
    public static function changeStatus($order, $newStatus, $ignoreDuplicate = false) {
        static::_doChangeStatus($order, $newStatus, $ignoreDuplicate);
        $order->save();
    }

    /**
     * Calc real purchased sum
     * @param $order
     * @param bool $refresh
     * @return int
     * @throws \Exception
     */
    public static function calcPurchasedProductsSum($order, $refresh = true) {

        if(empty($order->cachedProductsSum) || $refresh) {
            $s = new OrderItemsSearcher();
            $sums = $s->calcSums([
                'orderId' => $order->id,
                'onlyActive' => true
            ]);
            if(empty($sums)) {
                Utils::raise('Order with id ' . $order->id . ' not found');
            }

            $order->cachedProductsSum = intval($sums['realTotal']);
        }

        return $order->cachedProductsSum;
    }

}
