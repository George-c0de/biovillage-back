<?php

namespace Packages\Store\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\BaseApiController;
use App\Models\OrderModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Packages\Store\Http\Requests\StoreGiftOperation\ClearSystemPlaceRequest;
use Packages\Store\Http\Requests\StoreGiftOperation\GiftResidueRequest;
use Packages\Store\Http\Requests\StoreGiftOperation\PurchaserShortageRequest;
use Packages\Store\Http\Requests\StoreGiftOperation\PutRequest;
use Packages\Store\Http\Requests\StoreGiftOperation\TakeRequest;
use Packages\Store\Http\Requests\StoreGiftOperation\CorrectionRequest;
use Packages\Store\Models\StoreOperationModel;
use Packages\Store\Searchers\PurchaserShortageSearcher;
use Packages\Store\Searchers\StoreOperationsSearcher;
use Packages\Store\Service\StoreGiftOperationService;
use Packages\Store\Service\StoreOperationService;

class StoreGiftOperationController extends BaseApiController {

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function giftResidue(GiftResidueRequest $request, $id) {
        $result = StoreOperationsSearcher::giftResidue($id);
        return ResponseHelper::success($result);
    }

    /**
     * add to store
     * @param PutRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function put(PutRequest $request) {
        $validated = $request->validated();

        $validated['adminId'] = Auth::id();
        $validated['type'] = StoreOperationModel::PUT_TYPE;
        $validated['status'] = StoreOperationModel::COMPLETED_STATUS;

        $data = StoreGiftOperationService::put($validated);

        return ResponseHelper::success($data);
    }

    /**
     * take from the store
     * @param TakeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function take(TakeRequest $request) {
        $validated = $request->validated();

        $canMinus = StoreOperationService::canAdminMinus();
        $force = Arr::has($validated, 'force') ? $validated['force'] : false;

        $validated['adminId'] = Auth::id();
        $validated['type'] = StoreOperationModel::TAKE_TYPE;
        $validated['status'] = StoreOperationModel::COMPLETED_STATUS;

        $shortage = StoreGiftOperationService::checkTake($validated);

        if(!$canMinus && !$force && $shortage){
            return ResponseHelper::error($shortage);
        }

        $operation = StoreGiftOperationService::take($validated);
        $result = [];
        $result['operation'] = $operation;

        if($canMinus || $force){
            if($shortage) {
                $result['errors'] = $shortage;
                return ResponseHelper::success($result, 206);
            } else {
                return  ResponseHelper::success($result);
            }
        }
        
        return  ResponseHelper::success($result);
    }

    /**
     * correction in the store
     * @param CorrectionRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function correction(CorrectionRequest $request) {
        $validated = $request->validated();

        $validated['adminId'] = Auth::id();
        $validated['type'] = StoreOperationModel::CORRECTION_TYPE;
        $validated['status'] = StoreOperationModel::COMPLETED_STATUS;

        StoreGiftOperationService::reset([
            'storeId' => $validated['storeId'],
            'adminId' => $validated['adminId'],
            'type' => StoreOperationModel::RESET_CORRECTION_TYPE,
            'status' => StoreOperationModel::COMPLETED_STATUS,
        ]);
        $data = StoreGiftOperationService::put($validated);

        return ResponseHelper::success($data);
    }

    /**
     * resetting the system meta in the store
     * @param clearSystemPlaceRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function clearSystemPlace(ClearSystemPlaceRequest $request) {
        $validated = $request->validated();

        StoreGiftOperationService::reset([
            'storeId' => $validated['storeId'],
            'adminId' => Auth::id(),
            'type' => StoreOperationModel::PUT_TYPE,
            'status' => StoreOperationModel::COMPLETED_STATUS,
            'isSystem' => true,
        ]);

        return ResponseHelper::ok();
    }
}