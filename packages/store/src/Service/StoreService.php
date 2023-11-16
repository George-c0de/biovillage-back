<?php

namespace Packages\Store\Service;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Packages\Store\Converters\StoreConverter;
use Packages\Store\Models\StoreModel;
use Packages\Store\Models\StorePlaceModel;
use Packages\Store\Searchers\StoreContentsSearcher;

/**
 * Store Service. Working with the store
 */
class StoreService
{

    /**
     * store content
     * @param $id
     * @param $params
     * @return array
     */
    public static function contents($id, $params) {
        $searcher = new StoreContentsSearcher();
        $contents = $searcher->search($id, $params);
        return $contents;
    }

    /**
     * store creation
     * @param $params
     * @return mixed
     */
    public static function create($params) {
        $store = DB::transaction(function () use ($params) {
            $params['type'] = Arr::has($params, 'type') ? $params['type'] : StoreModel::TYPE_PRODUCT;

            $store = new StoreModel();
            $store->fill($params);
            $store->saveOrFail();

            $store = StoreModel::find($store['id']);
            $store = StoreConverter::singleToFront($store->toArray());

            // Create default places
            $defaultPlace = StorePlaceService::createDefault($store);

            $store = StoreModel::find($store['id']);
            $store->systemPlaceId = $defaultPlace['systemId'];
            $store->saveOrFail();

            return $store;
        });

        return $store;
    }

    /**
     * store update
     * @param $id
     * @param $params
     * @return array
     */
    public static function update($id, $params) {
        $store = StoreModel::find($id);
        $store->fill($params);
        $store->saveOrFail();

        $store = StoreModel::find($store['id']);
        $store = StoreConverter::singleToFront($store->toArray());

        return $store;
    }

    /**
     * deleting a store
     * @param $id
     */
    public static function delete($id) {
        DB::transaction(function () use ($id) {
            $store = StoreModel::find($id);
            $store->delete();
            StorePlaceModel::where('storeId', $id)->delete();
        });
    }
}
