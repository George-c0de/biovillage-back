<?php namespace App\Http\Controllers\Api;

use App\Components\Paginator;
use App\Helpers\Notify;
use App\Http\Requests\Order\CancelRequest;
use App\Http\Requests\Order\CompleteRequest;
use App\Http\Requests\Order\IndexRequest;
use App\Http\Requests\Order\OrderIdRequest;
use App\Http\Requests\Order\MyOrdersRequest;
use App\Http\Requests\Order\RefundRequest;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Requests\Order\UpdateRequest;
use App\Models\OrderModel;
use App\Service\PhoneService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Service\OrderService;

/**
 *
 */
class OrderController extends BaseApiController
{

    /**
     * Full complete order
     * @param CompleteRequest $request
     * @return JsonResponse
     */
    public function complete(CompleteRequest $request) {
        resolve('Billing')->completeOrder($request->id, $request->confirmSum);
        return ResponseHelper::success(
            OrderService::searchDetailsOne($request->id)
        );
    }

    /**
     * Cancel order
     * @param CancelRequest $request
     * @return JsonResponse
     */
    public function cancel(CancelRequest $request) {
        resolve('Billing')->cancelOrder($request->id);
        return ResponseHelper::success(
            OrderService::searchDetailsOne($request->id)
        );
    }

    /**
     * Full refund order and cancel
     * @param RefundRequest $request
     * @return JsonResponse
     */
    public function refund(RefundRequest $request) {
        resolve('Billing')->cancelAndRefundOrder($request->id);
        return ResponseHelper::success(
            OrderService::searchDetailsOne($request->id)
        );
    }

    /**
     * Add new order
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $o = OrderService::addOrder($request->validated());
        $orderInfo = OrderService::searchDetailsOne($o->id, true);
        $orderInfo['clientBonuses'] = resolve('Bonuses')->getAvailableBonuses($o->client());
        return ResponseHelper::success($orderInfo);
    }

    /**
     * List orders from admin
     * @param IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request) {
        $params = $request->validated();

        // Clear phone filter
        if(!empty($params['clientPhone'])) {
            $params['clientPhone'] = PhoneService::compact($params['clientPhone']);
        }

        $pager = new Paginator(
            OrderService::getCount($params),
            $params['perPage'] ?? OrderModel::DEFAULT_PER_PAGE,
            $params['page'] ?? 1
        );
        $params['limit'] = $pager->getLimit();
        $params['offset'] = $pager->getOffset();

        return ResponseHelper::success([
            'orders' => OrderService::search($params),
            'pager' => $pager->toArray()
        ]);
    }

    /**
     * Show orders for current user
     * @param MyOrdersRequest $request
     * @return JsonResponse
     */
    public function myOrders(MyOrdersRequest $request) {
        $params = $request->validated();
        $params['clientId'] = Auth::id();

        $pager = new Paginator(
            OrderService::getCount($params),
            $params['perPage'] ?? OrderModel::DEFAULT_PER_PAGE,
            $params['page'] ?? 1
        );
        $params['limit'] = $pager->getLimit();
        $params['offset'] = $pager->getOffset();
        $params['sort'] = 'created';
        $params['sortDirect'] = 'desc';

        return ResponseHelper::success([
            'orders' => OrderService::searchDetailed($params),
            'pager' => $pager->toArray()
        ]);
    }

    /**
     * Show all order requests
     * @param OrderIdRequest $request
     * @return JsonResponse
     */
    public function items(OrderIdRequest $request) {
        return ResponseHelper::success([
            'items' => OrderService::items($request->id)
        ]);
    }

    /**
     * Show all order payments
     * @param OrderIdRequest $request
     * @return JsonResponse
     */
    public function payments(OrderIdRequest $request) {
        return ResponseHelper::success([
            'payments' => OrderService::payments($request->id)
        ]);
    }

    /**
     * Show order gifts
     * @param OrderIdRequest $request
     * @return JsonResponse
     */
    public function gifts(OrderIdRequest $request) {
        return ResponseHelper::success([
            'gifts' => OrderService::gifts($request->id)
        ]);
    }

    /**
     * Update orders info
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(UpdateRequest $request) {
        OrderService::updateOrder($request->validated());
        return ResponseHelper::success(
            OrderService::searchDetailsOne($request->id)
        );
    }

    /**
     * Show orders info
     * @param OrderIdRequest $request
     * @return array|JsonResponse|null
     */
    public function show(OrderIdRequest $request) {
        return ResponseHelper::success([
            'info' => OrderService::searchOne(['id' => $request->id]),
            'payments' => OrderService::payments($request->id),
            'gifts' => OrderService::gifts($request->id),
            'items' => OrderService::items($request->id)
        ]);
    }
}
