<?php

namespace Packages\Store\Service;

use App\Helpers\DbHelper;
use App\Models\ProductModel;
use App\Models\SettingsModel;
use App\Service\CustomModelService;
use App\Service\SettingsService;
use App\Service\StorageService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Packages\Store\Models\StoreModel;
use Packages\Store\Models\StoreOperationContentModel;
use Packages\Store\Models\StoreOperationFileModel;
use Packages\Store\Models\StoreOperationModel;
use Packages\Store\Searchers\LastProductsSearcher;
use Packages\Store\Searchers\RealUnitsProductsSearcher;

/**
 * Store Operation Service. Working with operations in the store
 */
class StoreOperationService
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

        // create an auxiliary array key productId | storePlaceId value realUnits
        $storePlaceIds = [];
        $productIds = [];
        $helpChangeContent = [];
        foreach ($params['contents'] as $i => $content) {
            $productIds[] = $content['productId'];
            $storePlaceIds[] = $content['storePlaceId'];
            $key = $content['productId'].'|'.$content['storePlaceId'];
            $helpChangeContent[$key] = abs($content['realUnits']);
        }

        // requesting the number of products in the store
        $realUnitsProducts = RealUnitsProductsSearcher::search([
            'storeId' => $params['storeId'],
            'productIds' => $productIds,
            'storePlaceIds' => $storePlaceIds,
        ]);

        if($realUnitsProducts){
            foreach ($realUnitsProducts as $item) {
                // only if the base unit is equal to the current one
                if($item['unitId'] == $item['unitIdBase']){
                    $key = $item['productId'].'|'.$item['storePlaceId'];

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
        }

        $result = [];
        foreach ($helpChangeContent as $key => $value) {
            $productPlace = explode('|', $key);
            $productId = $productPlace[0];
            $storePlaceId = $productPlace[1];

            $result[] = [
                'productId' => (int) $productId,
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

            $realUnitsProducts = RealUnitsProductsSearcher::search($searchParams);
            $productIds = [];

            $resetContent = [];
            foreach ($realUnitsProducts as $item) {
                if ($item['realUnits']) {
                    $productIds[] = $item['productId'];
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
                self::setRealUnitsForProduct($productIds);
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
            $productIds = [];
            foreach ($contents as $item) {
                $productIds[] = $item['productId'];
            }
            $productIds = array_unique($productIds);
            $products = ProductModel::whereIn('id', $productIds)
                ->get()
                ->toArray();

            foreach ($contents as &$item) {
                $item['storeId'] = $params['storeId'];
                $key = array_search($item['productId'], array_column($products, 'id'));
                $item['unitId'] = $products[$key]['unitId'];
                $item['storeOperationId'] = $storeOperationId;

                $netCostPerStep = ($item['netCost'] / $item['realUnits']) * $products[$key]['unitStep'];
                $netCostPerStep = intval(floor($netCostPerStep));
                $item['netCostPerStep'] = $netCostPerStep;

                $product = ProductModel::find($products[$key]['id']);
                $product->fill([
                    'netCostPerStep' => $netCostPerStep
                ]);
                $product->saveOrFail();
            }

            StoreOperationContentModel::insert($contents);
            self::setRealUnitsForProduct($productIds);
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
        $productIds = [];
        $storeOperation = self::createOperation($params);

        // create an auxiliary array key productId | storePlaceId value realUnits
        $helpChangeContent = [];
        foreach ($params['contents'] as $content) {
            $productIds[] = $content['productId'];
            $storePlaceIds[] = $content['storePlaceId'];
            $key = $content['productId'].'|'.$content['storePlaceId'];
            $helpChangeContent[$key] = abs($content['realUnits']);
        }

        // request for information about the realUnits of products in the store
        $realUnitsProducts = RealUnitsProductsSearcher::search([
            'storeId' => $params['storeId'],
            'productIds' => $productIds,
            'storePlaceIds' => $storePlaceIds,
        ]);

        $storeIds = [$params['storeId']];
        [$storeIds, $helpChangeContent] = self::takeExistProducts($storeOperation['id'], $realUnitsProducts, $helpChangeContent, $storeIds);
        $force = Arr::has($params, 'force') ? $params['force'] : false;
        $storeIds = self::takeMinusProducts($storeOperation['id'], $force, $helpChangeContent, $storeIds);
        self::setRealUnitsForProduct($productIds);

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
     * @param $realUnitsProducts
     * @param $helpChangeContent
     * @param $storeIds
     * @return mixed
     */
    public static function takeExistProducts($storeOperationId, $realUnitsProducts, $helpChangeContent, $storeIds){
        // collect an array of take products from the store
        $storeChangeContent = [];
        if($realUnitsProducts){

            foreach ($realUnitsProducts as $item) {
                // only if the base unit is equal to the current one, do take operation
                if($item['unitId'] == $item['unitIdBase']){
                    $key = $item['productId'].'|'.$item['storePlaceId'];

                    // if there is no such key, then we skip iteration
                    if(!Arr::has($helpChangeContent, $key)) continue;

                    $changeRealUnits = $helpChangeContent[$key];

                    if ($changeRealUnits) {
                        unset($item['unitIdBase']);
                        $item['storeId'] = $storeIds[0];
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
    public static function takeMinusProducts($storeOperationId, $force, $helpChangeContent, $storeIds){
        // if we don't have any products in the store, we go into a minus, minus we write down to a service place
        // we take the last realUnits and price on arrival
        $canMinus = self::canAdminMinus();
        $isMinus = $canMinus ? true : $force ? true : false;
        $storeId = $storeIds[0];

        if ($helpChangeContent && $isMinus) {

            // changing the structure of the $ helpChangeContent array
            // key productId value realUnits
            $productIds = [];
            foreach ($helpChangeContent as $key => $realUnits) {
                $productId = explode('|', $key)[0];
                $productIds[] = $productId;

                unset($helpChangeContent[$key]);
                // if we take the same product from different places and go on credit
                // then we add up the total credit by product
                if(!Arr::has($helpChangeContent, $productId)) {
                    $helpChangeContent[$productId] = $realUnits;
                } else {
                    $helpChangeContent[$productId] += $realUnits;
                }
            }

            // For the minus, we find the last arrival of the product in the store
            $takeCredit = LastProductsSearcher::search([
                'storeId' => $storeId,
                'productIds' => $productIds,
            ]);

            // if the products exist, then we take all parameters from these records.
            if ($takeCredit) {
                foreach ($takeCredit as &$item) {
                    $storeIds[] = $item['storeId'];
                    $item['storeOperationId'] = $storeOperationId;
                    $item['realUnits'] = -1 * abs($helpChangeContent[$item['productId']]);
                    $item['netCost'] = abs($item['realUnits']) * $item['netCostPerStep'] / $item['unitStep'];
                    unset($item['unitStep']);
                }
            // if there have never been products, then we go into the red with default parameters
            } else {
                $takeDefault = self::takeDefaultSystem($storeOperationId, $productIds, $helpChangeContent, $storeId);
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
     * @param $productIds
     * @param $helpChangeContent
     * @param $storeId
     * @return array
     */
    public static function takeDefaultSystem($storeOperationId, $productIds, $helpChangeContent, $storeId = false) {
        $products = ProductModel::whereIn('id', $productIds)
            ->get()
            ->toArray();

        if($storeId) {
            $store = StoreModel::find($storeId)
                ->toArray();
        } else {
            $store = StoreModel::where('type', StoreModel::TYPE_PRODUCT)
                ->orderBy('order', 'ASC')
                ->first()
                ->toArray();
        }


        $takeCredit = [];
        foreach ($helpChangeContent as $productId => $realUnits) {
            $key = array_search($productId, array_column($products, 'id'));

            $takeCredit[] = [
                'storeOperationId' => $storeOperationId,
                'productId' => $productId,
                'realUnits' => -1 * abs($realUnits),
                'storePlaceId' => $store['systemPlaceId'],
                'storeId' => $store['id'],
                'unitId' => $products[$key]['unitId'],
                'netCostPerStep' => $products[$key]['netCostPerStep'],
                'netCost' => abs($realUnits) * $products[$key]['netCostPerStep'] / $products[$key]['unitStep'],
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

    /**
     * set realUnits for Product
     * @param $productIds
     * @throws \Exception
     */
    public static function setRealUnitsForProduct($productIds) {
        $realUnitsProducts = RealUnitsProductsSearcher::searchQuick([
            'productIds' => $productIds
        ]);

        if($realUnitsProducts){
            $productsCustomModel = new CustomModelService(with(new ProductModel())->getTable());
            $productsCustomModel->updateMultiple([
                ['id' => 'type:integer'],
                ['realUnits' => 'type:integer']
            ], $realUnitsProducts);
        }
    }
}
