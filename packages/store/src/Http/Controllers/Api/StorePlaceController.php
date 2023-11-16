<?php

namespace Packages\Store\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\BaseApiController;
use Packages\Store\Converters\StorePlaceConverter;
use Packages\Store\Http\Requests\StorePlace\DeleteRequest;
use Packages\Store\Http\Requests\StorePlace\IndexRequest;
use Packages\Store\Http\Requests\StorePlace\ShowRequest;
use Packages\Store\Http\Requests\StorePlace\StoreRequest;
use Packages\Store\Http\Requests\StorePlace\UpdateRequest;
use Packages\Store\Models\StorePlaceModel;

class StorePlaceController extends BaseApiController {

    /**
     * list of places in the store
     * @param IndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IndexRequest $request) {
        $storePlaces = StorePlaceModel::all();
        $storePlaces = StorePlaceConverter::collectionToFront($storePlaces->toArray());
        return ResponseHelper::success([
            'storePlaces' => $storePlaces
        ]);
    }

    /**
     * detailed information on the place in the store
     * @param ShowRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShowRequest $request, $id) {
        $storePlaces = StorePlaceModel::where('storeId', $id)->get()->toArray();
        $storePlaces = StorePlaceConverter::collectionToFront($storePlaces);
        return ResponseHelper::success($storePlaces);
    }

    /**
     * creating place in the store
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function store(StoreRequest $request) {
        $validated = $request->validated();
        $storePlace = new StorePlaceModel();
        $storePlace->fill($validated);
        $storePlace->saveOrFail();

        $storePlace = StorePlaceModel::find($storePlace['id']);
        $storePlace = StorePlaceConverter::singleToFront($storePlace->toArray());
        return ResponseHelper::success($storePlace);
    }

    /**
     * store place update
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $id) {
        $validated = $request->validated();
        $storePlace = StorePlaceModel::find($id);
        $storePlace->fill($validated);
        $storePlace->saveOrFail();

        $storePlace = StorePlaceModel::find($storePlace['id']);
        $storePlace = StorePlaceConverter::singleToFront($storePlace->toArray());
        return ResponseHelper::success($storePlace);
    }

    /**
     * remove store place
     * @param DeleteRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteRequest $request, $id) {
        $storePlace = StorePlaceModel::find($id);
        $storePlace->delete();
        return ResponseHelper::ok();
    }
}
