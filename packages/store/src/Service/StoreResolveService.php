<?php

namespace Packages\Store\Service;

use App\Models\OrderGiftModel;
use App\Models\OrderItemModel;
use Illuminate\Support\Arr;
use Packages\Store\Models\StoreOperationContentModel;
use Packages\Store\Models\StoreOperationModel;
use Packages\Store\Searchers\LastGiftsSearcher;
use Packages\Store\Searchers\LastProductsSearcher;
use Packages\Store\Searchers\RealUnitsGiftsSearcher;
use Packages\Store\Searchers\RealUnitsProductsSearcher;

/**
 * service for external use
 */
class StoreResolveService
{

    /**
     * check for minus before reserve an order
     * @param $orderId
     * @param $products
     * @param $gifts
     * @return array
     */
    public function orderingCheckReserve($orderId, $products, $gifts){
        $storeOperation = StoreOperationModel::where('orderId', $orderId)->first();
        $result = [];

        $productsErrors = self::checkProducts($storeOperation, $products);
        $giftsErrors = self::checkGifts($storeOperation, $gifts);

        if($productsErrors || $giftsErrors){
            $result = [
                'products' => $productsErrors,
                'gifts' => $giftsErrors,
            ];
        }

        return $result;
    }

    /**
     * @param $storeOperation
     * @param $products
     * @return array
     */
    public function checkProducts($storeOperation, $products) {
        $result = [];

        if($products){
            $productIds = [];
            $helpProductsContent = [];

            foreach ($products as $item) {
                if((int) $item['realUnits'] !== 0){
                    $productIds[] = $item['productId'];
                    $helpProductsContent[$item['productId']] = $item['realUnits'];
                }
            }

            if($storeOperation){
                $realUnitsProducts = RealUnitsProductsSearcher::search([
                    'notStoreOperationId' => $storeOperation['id'],
                    'productIds' => $productIds,
                ], true);
            } else {
                $realUnitsProducts = RealUnitsProductsSearcher::search([
                    'productIds' => $productIds,
                ], true);
            }

            foreach ($realUnitsProducts as $item) {
                // only if the base unit is equal to the current one
                if($item['unitId'] == $item['unitIdBase']){
                    // if there is no such key, then we skip iteration
                    if(!Arr::has($helpProductsContent, $item['productId'])) continue;
                    $changeRealUnits = $helpProductsContent[$item['productId']];
                    if ($changeRealUnits) {
                        if ($item['realUnits'] >= $changeRealUnits) {
                            unset($helpProductsContent[$item['productId']]);
                        } else {
                            $helpProductsContent[$item['productId']] = $helpProductsContent[$item['productId']] - $item['realUnits'];
                        }
                    }
                }
            }

            if($helpProductsContent) {
                foreach ($helpProductsContent as $productId => $value) {
                    $result[] = [
                        'productId' => (int) $productId,
                        'shortage' =>  abs((int) $value),
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * @param $storeOperation
     * @param $gifts
     * @return array
     */
    public function checkGifts($storeOperation, $gifts) {
        $result =[];

        if($gifts){
            $giftIds = [];
            $helpGiftsContent = [];
            foreach ($gifts as $item) {
                if((int) $item['realQty'] !== 0){
                    $giftIds[] = $item['giftId'];
                    $helpGiftsContent[$item['giftId']] = $item['realQty'];
                }
            }

            if($storeOperation){
                $realUnitsGifts = RealUnitsGiftsSearcher::search([
                    'notStoreOperationId' => $storeOperation['id'],
                    'giftIds' => $giftIds,
                ], true);
            } else {
                $realUnitsGifts = RealUnitsGiftsSearcher::search([
                    'giftIds' => $giftIds,
                ], true);
            }

            foreach ($realUnitsGifts as $item) {
                // if there is no such key, then we skip iteration
                if(!Arr::has($helpGiftsContent, $item['giftId'])) continue;
                $changeRealUnits = $helpGiftsContent[$item['giftId']];
                if ($changeRealUnits) {
                    if ($item['realUnits'] >= $changeRealUnits) {
                        unset($helpGiftsContent[$item['giftId']]);
                    } else {
                        $helpGiftsContent[$item['giftId']] = $helpGiftsContent[$item['giftId']] - $item['realUnits'];
                    }
                }

            }

            if($helpGiftsContent){
                foreach ($helpGiftsContent as $giftId => $value) {
                    $result[] = [
                        'giftId' => (int) $giftId,
                        'shortage' =>  abs((int) $value),
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * reserve an order
     * @param $order
     * @return StoreOperationModel
     * @throws \Throwable
     */
    public function orderingReserve($order) {

        $storeOperation = StoreOperationModel::where('orderId', $order['id'])
            ->first();
        if($storeOperation){
            $storeOperation = $storeOperation->toArray();
        }  else {
            $storeOperation = StoreOperationService::createOperation([
                'clientId' => $order['clientId'],
                'orderId' => $order['id'],
                'type' => StoreOperationModel::ORDERING_TYPE,
                'status' => StoreOperationModel::RESERVE_STATUS,
            ]);
        }

        // if there are old records, delete
        $storeContentModel =  StoreOperationContentModel::where('storeOperationId', $storeOperation['id']);
        if($storeContentModel->exists()) {
            $storeContentModel->delete();
        }

        [$productIds, $helpProductsContent] = self::getProductsInfo($order['id']);
        [$storeProductIds, $helpProductsContent] = self::takeExistProducts($storeOperation['id'], $productIds, $helpProductsContent);
        $storeProductIds = self::takeMinusProducts($storeOperation['id'], $helpProductsContent, $storeProductIds);
        StoreOperationService::setRealUnitsForProduct($productIds);

        [$giftsIds, $helpGiftsContent] = self::getGiftsInfo($order['id']);
        [$storeGiftIds, $helpGiftsContent] = self::takeExistGifts($storeOperation['id'], $helpGiftsContent, [
            'notStoreOperationId' => $storeOperation['id'],
            'giftIds' => $giftsIds,
        ]);
        $storeGiftIds = self::takeMinusGifts($storeOperation['id'], $helpGiftsContent, $storeGiftIds);

        $storeIds = array_merge($storeProductIds, $storeGiftIds);

        // we can reserve products from different stores, enter information into operation
        StoreOperationService::changeMultipleStores($storeOperation['id'], $storeIds);

        return $storeOperation;
    }

    public static function getProductsInfo($orderId) {
        $orderItems = OrderItemModel::where('orderId', $orderId)
            ->get()
            ->toArray();
        $helpProductsContent = [];
        $productIds = [];
        foreach ($orderItems as $item) {
            $productIds[] = $item['productId'];
            $helpProductsContent[$item['productId']] = $item['realUnits'];
        }

        return [
            $productIds,
            $helpProductsContent,
        ];
    }

    public static function getGiftsInfo($orderId) {
        $orderGifts = OrderGiftModel::where('orderId', $orderId)
            ->get()
            ->toArray();
        $helpGiftsContent = [];
        $giftsIds = [];

        if($orderGifts){
            foreach ($orderGifts as $item) {
                $giftsIds[] = $item['giftId'];
                $helpGiftsContent[$item['giftId']] = $item['realQty'];
            }
        }

        return [
            $giftsIds,
            $helpGiftsContent,
        ];
    }

    /**
     * take existing products
     * @param $storeOperationId
     * @param $productIds
     * @param $helpProductsContent
     * @return array
     */
    public function takeExistProducts($storeOperationId, $productIds, $helpProductsContent) {

        // request for information about the realUnits of products in the store
        $realUnitsProducts = RealUnitsProductsSearcher::search([
            'notStoreOperationId' => $storeOperationId,
            'productIds' => $productIds,
        ]);

        $storeIds = [];
        $storeChangeContent = [];
        if($realUnitsProducts){
            foreach ($realUnitsProducts as $item) {
                // only if the base unit is equal to the current one, we perform the take operation
                if($item['unitId'] == $item['unitIdBase']){

                    // if there is no such key, then we skip iteration
                    if(!Arr::has($helpProductsContent, $item['productId'])) continue;

                    $changeRealUnits = $helpProductsContent[$item['productId']];

                    if ($changeRealUnits) {
                        $storeIds[] = $item['storeId'];
                        unset($item['unitIdBase']);
                        $item['storeOperationId'] = $storeOperationId;
                        if ($item['realUnits'] >= $changeRealUnits) {
                            $item['realUnits'] = -1 * $changeRealUnits;
                            unset($helpProductsContent[$item['productId']]);
                        } else {
                            $item['realUnits'] = -1 * $item['realUnits'];
                            $helpProductsContent[$item['productId']] = $helpProductsContent[$item['productId']] + $item['realUnits'];
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

        return [$storeIds, $helpProductsContent];
    }

    /**
     * take existing gifts
     * @param $storeOperationId
     * @param $helpGiftsContent
     * @param $searchParams
     * @return array
     */
    public function takeExistGifts($storeOperationId, $helpGiftsContent, $searchParams){
        $storeIds = [];

        // request for information about the realUnits of products in the store
        $realUnitsGifts = RealUnitsGiftsSearcher::search($searchParams);


        $storeChangeContent = [];
        if ($realUnitsGifts) {
            foreach ($realUnitsGifts as $item) {
                // if there is no such key, then we skip iteration
                if (!Arr::has($helpGiftsContent, $item['giftId'])) continue;

                $changeRealUnits = $helpGiftsContent[$item['giftId']];

                if ($changeRealUnits) {
                    $storeIds[] = $item['storeId'];
                    $item['storeOperationId'] = $storeOperationId;
                    if ($item['realUnits'] >= $changeRealUnits) {
                        $item['realUnits'] = -1 * $changeRealUnits;
                        unset($helpGiftsContent[$item['giftId']]);
                    } else {
                        $item['realUnits'] = -1 * $item['realUnits'];
                        $helpGiftsContent[$item['giftId']] = $helpGiftsContent[$item['giftId']] + $item['realUnits'];
                    }
                    $storeChangeContent[] = $item;
                }
            }
        }

        // take existing product
        if ($storeChangeContent) {
            StoreOperationContentModel::insert($storeChangeContent);
        }


        return [$storeIds, $helpGiftsContent];
    }

    /**
     * take non-existing products
     * @param $storeOperationId
     * @param $helpProductsContent
     * @param $storeIds
     * @return array
     */
    public function takeMinusProducts($storeOperationId, $helpProductsContent, $storeIds) {
        // if we don't have any products in the store, we go into a minus, minus we write down to a service place
        // we take the last realUnits and price on arrival
        if ($helpProductsContent) {

            $productIds = array_keys($helpProductsContent);
            $takeCredit = LastProductsSearcher::search([
                'productIds' => $productIds
            ]);

            if ($takeCredit) {
                foreach ($takeCredit as &$item) {
                    $storeIds[] = $item['storeId'];
                    $item['storeOperationId'] = $storeOperationId;
                    $item['realUnits'] = -1 * abs($helpProductsContent[$item['productId']]);
                    $item['netCost'] = abs($item['realUnits']) * $item['netCostPerStep'] / $item['unitStep'];
                    unset($item['unitStep']);
                }
            } else {
                $takeDefault = StoreOperationService::takeDefaultSystem($storeOperationId, $productIds, $helpProductsContent);
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
     * take non-existing products
     * @param $storeOperationId
     * @param $helpGiftsContent
     * @param $storeIds
     * @return array
     */
    public function takeMinusGifts($storeOperationId, $helpGiftsContent, $storeIds) {
        // if we don't have any products in the store, we go into a minus, minus we write down to a service place
        // we take the last realUnits and price on arrival
        if ($helpGiftsContent) {

            $giftIds = array_keys($helpGiftsContent);
            $takeCredit = LastGiftsSearcher::search([
                'giftIds' => $giftIds
            ]);

            if ($takeCredit) {
                foreach ($takeCredit as &$item) {
                    $storeIds[] = $item['storeId'];
                    $item['storeOperationId'] = $storeOperationId;
                    $item['netCost'] = $item['netCostPerStep'] * abs($helpGiftsContent[$item['giftId']]);
                    $item['realUnits'] = -1 * abs($helpGiftsContent[$item['giftId']]);
                }
            } else {
                $takeDefault = StoreGiftOperationService::takeDefaultSystem($storeOperationId, $giftIds, $helpGiftsContent);
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
     * set status completed by order
     * @param $order
     * @return mixed
     */
    public function orderingCompleted($order) {
        $order = $order->toArray();
        $storeOperation = StoreOperationModel::where('orderId', $order['id'])->first();
        if($storeOperation) {
            $storeOperation->status = StoreOperationModel::COMPLETED_STATUS;
            $storeOperation->saveOrFail();
        }
        return $storeOperation;
    }

    /**
     * set status cancel by order
     * @param $order
     * @return mixed
     */
    public function orderingCancel($order) {
        $order = $order->toArray();
        $storeOperation = StoreOperationModel::where('orderId', $order['id'])->first();
        if($storeOperation){
            $storeOperation->status = StoreOperationModel::CANCEL_STATUS;
            $storeOperation->saveOrFail();
        }
        return $storeOperation;
    }
}
