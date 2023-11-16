<?php

namespace Packages\Store\Service;

use App\Helpers\DbHelper;
use App\Models\GiftModel;
use App\Models\SettingsModel;
use App\Service\SettingsService;
use App\Service\StorageService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Packages\Store\Models\StoreModel;
use Packages\Store\Models\StoreOperationContentModel;
use Packages\Store\Models\StoreOperationFileModel;
use Packages\Store\Models\StoreOperationModel;
use Packages\Store\Searchers\LastGiftsSearcher;
use Packages\Store\Searchers\RealUnitsGiftsSearcher;

/**
 * Store Operation Service. Working with operations in the store
 */
class StoreGiftOperationService
{

    /**
     * add to store
     * @param $params
     * @return StoreOperationModel
     * @throws \Throwable
     */
    public static function put($params){
        $storeOperation = DB::transaction(function () use ($params) {
            $storeOperation = self::createOperation($params);
            self::putContents($storeOperation['id'], $params);
            $files = self::addFiles($storeOperation['id'], $params);
            $storeOperation['files'] = $files;

            return $storeOperation;
        });

        return $storeOperation;
    }

    /**
     * check before take
     * @param $params
     * @return array
     */
    public static function checkTake($params) {

        // create an auxiliary array key giftId | storePlaceId value realUnits
        $storePlaceIds = [];
        $giftIds = [];
        $helpChangeContent = [];
        foreach ($params['contents'] as $i => $content) {
            $giftIds[] = $content['giftId'];
            $storePlaceIds[] = $content['storePlaceId'];
            $key = $content['giftId'].'|'.$content['storePlaceId'];
            $helpChangeContent[$key] = abs($content['realUnits']);
        }

        // requesting the number of products in the store
        $realUnitsGifts = RealUnitsGiftsSearcher::search([
            'storeId' => $params['storeId'],
            'giftIds' => $giftIds,
            'storePlaceIds' => $storePlaceIds,
        ]);

        if($realUnitsGifts){
            foreach ($realUnitsGifts as $item) {
                $key = $item['giftId'].'|'.$item['storePlaceId'];

                // if there is no such key, then we skip iteration
                if(!Arr::has($helpChangeContent, $key)) continue;

                $changeRealUnits = $helpChangeContent[$key];

                if ($changeRealUnits) {
                    if ($item['realUnits'] >= $changeRealUnits) {
                        unset($helpChangeContent[$key]);
                    } else {
                        $helpChangeContent[$key] = $helpChangeContent[$key] + $item['realUnits'];
                    }
                }
            }
        }

        $result = [];
        foreach ($helpChangeContent as $key => $value) {
            $productPlace = explode('|', $key);
            $giftIds = $productPlace[0];
            $storePlaceId = $productPlace[1];

            $result[] = [
                'giftId' => (int) $giftIds,
                'storePlaceId' => (int) $storePlaceId,
                'shortage' =>  -1 * abs((int) $value),
            ];
        }

        return $result;
    }

    /**
     * take from the store
     * @param $params
     * @return mixed
     */
    public static function take($params) {
        $storeOperation = DB::transaction(function () use ($params) {
            $storeOperation = self::takeContents($params);
            if($storeOperation) {
                $files = self::addFiles($storeOperation['id'], $params);
                $storeOperation['files'] = $files;
            }

            return $storeOperation;
        });

        return $storeOperation;
    }

    /**
     * reset all products in the store
     * @param $params
     * @throws \Throwable
     */
    public static function reset($params){
        DB::transaction(function () use ($params) {
            $searchParams = [
                'storeId' => $params['storeId'],
            ];

            if(Arr::has($params, 'isSystem') && $params['isSystem']) $searchParams['isSystem'] = $params['isSystem'];

            $realUnitsGifts = RealUnitsGiftsSearcher::search($searchParams);
            $giftIds = [];

            $resetContent = [];
            foreach ($realUnitsGifts as $item) {
                if ($item['realUnits']) {
                    $giftIds[] = $item['giftId'];
                    $item['realUnits'] = -1 * $item['realUnits'];
                    $resetContent[] = $item;
                }
            }

            if ($resetContent) {
                $storeOperation = self::createOperation([
                    'storeId' => $params['storeId'],
                    'status' => $params['status'],
                    'type' => $params['type'],
                    'adminId' => $params['adminId'],
                ]);

                foreach ($resetContent as &$item) {
                    unset($item['unitIdBase']);
                    $item['storeOperationId'] = $storeOperation['id'];
                }

                StoreOperationContentModel::insert($resetContent);
            }
        });
    }

    /**
     * operation creation
     * @param $params
     * @return StoreOperationModel
     * @throws \Throwable
     */
    public static function createOperation($params) {

        if(Arr::has($params, 'storeId')){
            $params['storeIds'] = DbHelper::arrayToPgArray([$params['storeId']]);
            unset($params['storeId']);
        }

        $storeOperation = new StoreOperationModel();
        $storeOperation->fill($params);
        $storeOperation->saveOrFail();

        $storeOperation = StoreOperationModel::find($storeOperation['id']);

        return $storeOperation;
    }


    /**
     * add records of product arrival to the store
     * @param $storeOperationId
     * @param $params
     * @throws \Exception
     */
    public static function putContents($storeOperationId, $params) {

        if(Arr::has($params, 'contents')) {
            $contents = $params['contents'];

            $giftIds = [];
            foreach ($contents as $item) {
                $giftIds[] = $item['giftId'];
            }
            $gifts = GiftModel::whereIn('id', $giftIds)->get()->toArray();

            foreach ($contents as &$item) {
                $item['storeId'] = $params['storeId'];
                $key = array_search($item['giftId'], array_column($gifts, 'id'));

                $item['netCostPerStep'] =  $gifts[$key]['price'];
                $item['netCost'] = $item['realUnits'] * $gifts[$key]['price'];
                $item['storeOperationId'] = $storeOperationId;
            }

            StoreOperationContentModel::insert($contents);
        }
    }


    /**
     * add records about taking products from the store
     * @param $params
     * @return StoreOperationModel|null
     * @throws \Throwable
     */
    public static function takeContents($params) {

        $storePlaceIds = [];
        $giftIds = [];
        $storeOperation = self::createOperation($params);

        // create an auxiliary array key productId | storePlaceId value realUnits
        $helpChangeContent = [];
        foreach ($params['contents'] as $content) {
            $giftIds[] = $content['giftId'];
            $storePlaceIds[] = $content['storePlaceId'];
            $key = $content['giftId'].'|'.$content['storePlaceId'];
            $helpChangeContent[$key] = abs($content['realUnits']);
        }

        $storeIds = [$params['storeId']];
        [$storeIds, $helpChangeContent] = self::takeExistGifts($storeOperation['id'], $helpChangeContent, [
            'storeId' => $params['storeId'],
            'giftIds' => $giftIds,
            'storePlaceIds' => $storePlaceIds,
        ], $storeIds);
        $force = Arr::has($params, 'force') ? $params['force'] : false;
        $storeIds = self::takeMinusGifts($storeOperation['id'], $force, $helpChangeContent, $storeIds);

        // we can reserve products from different stores, enter information into operation
        self::changeMultipleStores($storeOperation['id'], $storeIds);

        // $storeOperation can be empty, for example, when we cannot go into negative territory and there are no products in the store
        // in this case, delete the entry
        $isContent = StoreOperationContentModel::where('storeOperationId', $storeOperation['id'])->exists();
        if(!$isContent) {
            $storeOperation->delete();
            $storeOperation = null;
        }

        return $storeOperation;
    }

    /**
     * take existing products
     * @param $storeOperationId
     * @param $helpChangeContent
     * @param $storeIds
     * @param $searchParams
     * @return mixed
     */
    public static function takeExistGifts($storeOperationId, $helpChangeContent, $searchParams, $storeIds = []){

        // request for information about the realUnits of products in the store
        $realUnitsGifts = RealUnitsGiftsSearcher::search($searchParams);

        // collect an array of take products from the store
        $storeChangeContent = [];
        if($realUnitsGifts){

            foreach ($realUnitsGifts as $item) {
                $key = $item['giftId'].'|'.$item['storePlaceId'];

                // if there is no such key, then we skip iteration
                if(!Arr::has($helpChangeContent, $key)) continue;

                $changeRealUnits = $helpChangeContent[$key];

                if ($changeRealUnits) {
                    $storeIds[] = $item['storeId'];
                    $item['storeOperationId'] = $storeOperationId;
                    if ($item['realUnits'] >= $changeRealUnits) {
                        $item['realUnits'] = -1 * $changeRealUnits;
                        unset($helpChangeContent[$key]);
                    } else {
                        $item['realUnits'] = -1 * $item['realUnits'];
                        $helpChangeContent[$key] = $helpChangeContent[$key] + $item['realUnits'];
                    }
                    $storeChangeContent[] = $item;
                }
            }
        }

        // take existing product
        if ($storeChangeContent) {
            StoreOperationContentModel::insert($storeChangeContent);
        }

        return [$storeIds, $helpChangeContent];
    }

    /**
     * take non-existing products
     * @param $storeOperationId
     * @param $force
     * @param $helpChangeContent
     * @param $storeIds
     * @return array
     */
    public static function takeMinusGifts($storeOperationId, $force, $helpChangeContent, $storeIds){
        // if we don't have any products in the store, we go into a minus, minus we write down to a service place
        // we take the last realUnits and price on arrival
        $canMinus = self::canAdminMinus();
        $isMinus = $canMinus ? true : $force ? true : false;
        $storeId = $storeIds[0];

        if ($helpChangeContent && $isMinus) {

            // changing the structure of the $ helpChangeContent array
            // key productId value realUnits
            $giftIds = [];
            foreach ($helpChangeContent as $key => $realUnits) {
                $giftId = explode('|', $key)[0];
                $giftIds[] = $giftId;

                unset($helpChangeContent[$key]);
                // if we take the same product from different places and go on credit
                // then we add up the total credit by product
                if(!Arr::has($helpChangeContent, $giftId)) {
                    $helpChangeContent[$giftId] = $realUnits;
                } else {
                    $helpChangeContent[$giftId] += $realUnits;
                }
            }

            // For the minus, we find the last arrival of the product in the store
            $takeCredit = LastGiftsSearcher::search([
                'storeId' => $storeId,
                'giftIds' => $giftIds,
            ]);

            // if the products exist, then we take all parameters from these records.
            if ($takeCredit) {
                foreach ($takeCredit as &$item) {
                    $storeIds[] = $item['storeId'];
                    $item['storeOperationId'] = $storeOperationId;
                    $item['netCost'] = $item['netCostPerStep'] * abs($helpChangeContent[$item['giftId']]);
                    $item['realUnits'] = -1 * abs($helpChangeContent[$item['giftId']]);
                }
            // if there have never been products, then we go into the red with default parameters
            } else {
                $takeDefault = self::takeDefaultSystem($storeOperationId, $giftIds, $helpChangeContent, $storeId);
                $takeCredit = $takeDefault['takeCredit'];
                $storeIds[] = $takeDefault['storeId'];
            }

            // We take a loan for goods that need to be written off, but they are over
            // create credit for a systemic place in the store
            StoreOperationContentModel::insert($takeCredit);
        }

        return $storeIds;
    }
    /**
     * if changes affect several stores, enter information into the operation
     * @param $storeOperationId
     * @param $storeIds
     */
    public static function changeMultipleStores($storeOperationId, $storeIds) {
        if($storeIds) {
            $storeIds = array_map('intval', $storeIds);
            $storeIds = array_values($storeIds);
            $storeIds = array_unique($storeIds);
            $isMultipleStores = count($storeIds) > 1;
            $updateStoreOperation = StoreOperationModel::find($storeOperationId);
            if($updateStoreOperation['storeIds']) {
                $storeIds = array_merge(array_values(DbHelper::pgArrayToArray($updateStoreOperation['storeIds'])), $storeIds);
                $storeIds = array_unique($storeIds);
                $storeIds = array_values($storeIds);
            }
            $updateStoreOperation->storeIds = DbHelper::arrayToPgArray($storeIds);
            $updateStoreOperation->isMultipleStores = $isMultipleStores;
            $updateStoreOperation->saveOrFail();
        }
    }

    /**
     * take products with default parameters
     * @param $storeOperationId
     * @param $giftIds
     * @param $helpChangeContent
     * @param $storeId
     * @return array
     */
    public static function takeDefaultSystem($storeOperationId, $giftIds, $helpChangeContent, $storeId = false) {
        $gifts = GiftModel::whereIn('id', $giftIds)
            ->get()
            ->toArray();

        if($storeId) {
            $store = StoreModel::find($storeId)
                ->toArray();
        } else {
            $store = StoreModel::where('type', StoreModel::TYPE_GIFT)
                ->orderBy('order', 'ASC')
                ->first()
                ->toArray();
        }

        $takeCredit = [];
        foreach ($helpChangeContent as $giftId => $realUnits) {
            $key = array_search($giftId, array_column($gifts, 'id'));

            $takeCredit[] = [
                'storeOperationId' => $storeOperationId,
                'realUnits' => -1 * abs($realUnits),
                'storePlaceId' => $store['systemPlaceId'],
                'storeId' => $store['id'],
                'giftId' => $giftId,
                'netCostPerStep' => $gifts[$key]['price'],
                'netCost' => abs($realUnits) * $gifts[$key]['price'],
            ];
        }

        return [
            'storeId' => $store['id'],
            'takeCredit' => $takeCredit,
        ];
    }

    /**
     * add files to operation
     * @param $storeOperationId
     * @param $params
     * @return mixed|null
     */
    public static function addFiles($storeOperationId, $params){
        $files = null;
        if(Arr::has($params, 'files')){
            $files = $params['files'];

            foreach ($files as &$file) {
                $file['storeOperationId'] = $storeOperationId;
                $fileSrc = StorageService::saveMd5($file['src'], 'store');
                $file['src'] = $fileSrc;
            }

            StoreOperationFileModel::insert($files);
        }
        if($files) {
            $files = array_values($files);
            foreach ($files as &$file) {
                $file['src'] = Storage::url($file['src']);
            }
        }
        return $files;
    }

    /**
     * can the administrator go to the minus in the store
     * @return boolean
     */
    public static function canAdminMinus() {
        $isMinus = SettingsService::getSettingValue(SettingsModel::SETTING_STORE_CAN_ADMIN_MINUS);
        return $isMinus;
    }
}
