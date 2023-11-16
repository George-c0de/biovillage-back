<?php

namespace Packages\Store\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Packages\Store\Http\Requests\Store\StoreTypeRequest;
use Packages\Store\Http\Requests\StoreOperation\ClearSystemPlaceRequest;
use Packages\Store\Http\Requests\StoreOperation\IndexRequest;
use Packages\Store\Http\Requests\StoreOperation\ProductOperationsRequest;
use Packages\Store\Http\Requests\StoreOperation\ProductResidueRequest;
use Packages\Store\Http\Requests\StoreOperation\PurchaserShortageRequest;
use Packages\Store\Http\Requests\StoreOperation\PutRequest;
use Packages\Store\Http\Requests\StoreOperation\ShowRequest;
use Packages\Store\Http\Requests\StoreOperation\TakeRequest;
use Packages\Store\Http\Requests\StoreOperation\CorrectionRequest;
use Packages\Store\Models\StoreOperationModel;
use Packages\Store\Searchers\StoreOperationsSearcher;
use Packages\Store\Service\StoreOperationService;

class StoreOperationController extends BaseApiController {

    /**
     * list of operations
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request) {
        $searcher = new StoreOperationsSearcher();
        $operations = $searcher->index($request->validated());
        return ResponseHelper::success($operations);
    }

    /**
     * detailed information on the operation
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowRequest $reqest, $id) {
        $searcher = new StoreOperationsSearcher();
        $operation = $searcher->show($id);
        return ResponseHelper::success($operation);
    }

    /**
     * @param StoreTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function productOperations(ProductOperationsRequest $request, $id) {
        $result = StoreOperationsSearcher::productOperations($id);
        return ResponseHelper::success($result);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function productResidue(ProductResidueRequest $request, $id) {
        $result = StoreOperationsSearcher::productResidue($id);
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

        // Уже в копейках
        //foreach ($validated['contents'] as &$item) {
        //    $item['netCost'] = $item['netCost'] * 100;
        //}

        $data = StoreOperationService::put($validated);

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

        $shortage = StoreOperationService::checkTake($validated);

        if(!$canMinus && !$force && $shortage){
            return ResponseHelper::error($shortage);
        }

        $operation = StoreOperationService::take($validated);
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

        StoreOperationService::reset([
            'storeId' => $validated['storeId'],
            'adminId' => $validated['adminId'],
            'type' => StoreOperationModel::RESET_CORRECTION_TYPE,
            'status' => StoreOperationModel::COMPLETED_STATUS,
        ]);
        $data = StoreOperationService::put($validated);

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

        StoreOperationService::reset([
            'storeId' => $validated['storeId'],
            'adminId' => Auth::id(),
            'type' => StoreOperationModel::PUT_TYPE,
            'status' => StoreOperationModel::COMPLETED_STATUS,
            'isSystem' => true,
        ]);

        return ResponseHelper::ok();
    }
}