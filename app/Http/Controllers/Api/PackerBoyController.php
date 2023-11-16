<?php namespace App\Http\Controllers\Api;

use App\Http\Requests\PackerBoy\PackRequest;
use App\Http\Requests\PackerBoy\UnpackRequest;
use App\Models\OrderModel;
use App\Models\SettingsModel;
use App\Service\OrderItemService;
use App\Service\OrderService;
use App\Helpers\ResponseHelper;
use App\Service\PackageService;
use App\Service\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 *
 */
class PackerBoyController extends BaseApiController
{

    /**
     * Order is packed
     * @param PackRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function pack(PackRequest $request, $id) {

        $validated = $request->validated();
        $isStore = PackageService::hasPackage('Store');

        if(!$isStore){
            Db::transaction(function() use($request) {
                OrderItemService::massUpdate($request->items, $request->gifts);
                $order = OrderService::updateOrder([
                    'orderId' => $request->id,
                    'status' => OrderModel::STATUS_PACKED
                ]);
            });

            return ResponseHelper::ok();
        } else {

            $canMinus = SettingsService::getSettingValue(SettingsModel::SETTING_STORE_CAN_ADMIN_MINUS);
            $force = Arr::has($validated, 'force') ? $validated['force'] : false;
            $shortage = resolve('Store')->orderingCheckReserve($id, $request->items, $request->gifts);

            if(!$canMinus && !$force && $shortage){
                return ResponseHelper::error($shortage);
            }

            $order = Db::transaction(function() use($request) {
                OrderItemService::massUpdate($request->items, $request->gifts);
                $order = OrderService::updateOrder([
                    'orderId' => $request->id,
                    'status' => OrderModel::STATUS_PACKED
                ]);
                $order = OrderModel::find($order['id']);
                return $order;
            });

            resolve('Store')->orderingReserve($order);

            if($canMinus || $force){
                if($shortage) {
                    return ResponseHelper::success([
                        'errors' => $shortage,
                    ], 206);
                } else {
                    return  ResponseHelper::ok();
                }
            }

            return  ResponseHelper::ok();
        }
    }

    /**
     * Order is unpacked
     * @param UnpackRequest $request
     * @return JsonResponse
     */
    public function unpack(UnpackRequest $request) {
        OrderService::updateOrder([
            'orderId' => $request->id,
            'status' => OrderModel::STATUS_PLACED
        ]);
        return ResponseHelper::ok();
    }
}
