<?php

namespace Packages\Store\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\BaseApiController;
use Packages\Store\Converters\StoreConverter;
use Packages\Store\Http\Requests\Store\DeleteRequest;
use Packages\Store\Http\Requests\Store\IndexRequest;
use Packages\Store\Http\Requests\Store\ShowRequest;
use Packages\Store\Http\Requests\Store\ContentsRequest;
use Packages\Store\Http\Requests\Store\StoreRequest;
use Packages\Store\Http\Requests\Store\UpdateRequest;
use Packages\Store\Models\StoreModel;
use Packages\Store\Service\StoreService;

class StoreController extends BaseApiController {

    /**
     * store list
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request) {
        $stores = StoreModel::where($request->validated())->get();
        $stores = StoreConverter::collectionToFront($stores->toArray());
        return ResponseHelper::success([
            'stores' => $stores
        ]);
    }

    /**
     * detailed information on the store
     * @param ShowRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowRequest $request, $id) {
        $store = StoreModel::find($id);
        $store = StoreConverter::singleToFront($store->toArray());
        return ResponseHelper::success($store);
    }

    /**
     * store content
     * @param ContentsRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function contents(ContentsRequest $request, $id) {
        $storeContents = StoreService::contents($id, $request->validated());
        return ResponseHelper::success($storeContents);
    }

    /**
     * store creation
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(StoreRequest $request) {
        $store = StoreService::create($request->validated());
        return ResponseHelper::success($store);
    }

    /**
     * store update
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $id) {
        $store = StoreService::update($id, $request->validated());
        return ResponseHelper::success($store);
    }

    /**
     * deleting a store
     * @param DeleteRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteRequest $request, $id) {
        StoreService::delete($id);
        return ResponseHelper::ok();
    }
}
