<?php namespace App\Http\Controllers\Api;

use App\Components\Paginator;
use App\Http\Requests\OrderItem\MassUpdateItemRequest;
use App\Http\Requests\OrderItem\ShowGiftRequest;
use App\Http\Requests\OrderItem\UpdateItemRequest;
use App\Http\Requests\OrderItem\UpdateGiftRequest;
use App\Models\OrderModel;
use App\Service\OrderItemService;
use App\Service\OrderService;
use Illuminate\Http\Response;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\OrderItem\ShowItemRequest;

/**
 *
 */
class OrderItemController extends BaseApiController
{

    /**
     * Show item info
     * @param ShowItemRequest $request
     * @return JsonResponse
     */
    public function showItem(ShowItemRequest $request) {
        return ResponseHelper::success(
            OrderItemService::searchItemById($request->id)
        );
    }

    /**
     * Show item info
     * @param ShowGiftRequest $request
     * @return JsonResponse
     */
    public function showGift(ShowGiftRequest $request) {
        return ResponseHelper::success(
            OrderItemService::searchGiftById($request->id)
        );
    }

    /**
     * Update order item fields
     * @param UpdateItemRequest $request
     * @return JsonResponse
     */
    public function updateItem(UpdateItemRequest $request) {
        OrderItemService::updateItem($request->validated());
        return ResponseHelper::success(
            OrderItemService::searchItemById($request->id)
        );
    }

    /**
     * Update order item fields
     * @param UpdateGiftRequest $request
     * @return JsonResponse
     */
    public function updateGift(UpdateGiftRequest $request) {
        OrderItemService::updateGift($request->validated());
        return ResponseHelper::success(
            OrderItemService::searchGiftById($request->id)
        );
    }

    /**
     * Mass update order items
     * @param MassUpdateItemRequest $request
     * @return JsonResponse
     */
    public function massUpdate(MassUpdateItemRequest $request) {
        OrderItemService::massUpdate($request->items, $request->gifts);

        $updatedItems = [];
        if(!empty($request->items)) {
            $updatedItems = OrderItemService::searchItemsByIds(
                array_map(
                    function($item) {
                        return $item['id'];
                    },
                    $request->items
                )
            );
        }

        $updatedGifts = [];
        if(!empty($request->gifts)) {
            $updatedGifts = OrderItemService::searchOrderGiftsByIds(
                array_map(
                    function($item) {
                        return $item['id'];
                    },
                    $request->gifts
                )
            );
        }

        return ResponseHelper::success([
            'items' => $updatedItems,
            'gifts' => $updatedGifts
        ]);
    }

    /**
     * Cancel order item
     * @param ShowItemRequest $request
     * @return JsonResponse
     */
    public function cancelItem(ShowItemRequest $request) {
        OrderItemService::cancelOrderItem($request->id);
        return ResponseHelper::ok();
    }

    /**
     * Cancel order item
     * @param ShowGiftRequest $request
     * @return JsonResponse
     */
    public function cancelGift(ShowGiftRequest $request) {
        OrderItemService::cancelOrderGift($request->id);
        return ResponseHelper::ok();
    }
}
