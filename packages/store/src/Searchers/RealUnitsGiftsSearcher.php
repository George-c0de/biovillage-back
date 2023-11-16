<?php

namespace Packages\Store\Searchers;

use App\Helpers\Utils;
use Illuminate\Support\Facades\DB;
use Packages\Store\Models\StoreModel;

/**
 * search for the real units of products
 */
class RealUnitsGiftsSearcher
{
    /**
     * search
     * @param $params
     * @return array
     */
    public static function search($params, $notZero = true){
        $sql = '
            WITH "realUnits" AS (
                SELECT
                    soc."giftId",
                    soc."storeId",
                    soc."storePlaceId",
                    g."price" AS "netCostPerStep",
                    SUM(soc."realUnits") AS "realUnits"
                    
                FROM "storeOperationContents" AS soc
                
                LEFT JOIN "stores" AS s
                    ON s."id" = soc."storeId" 
                
                LEFT JOIN "storePlaces" AS sp
                    ON sp."id" = soc."storePlaceId" 
                
                LEFT JOIN "gifts" AS g
                    ON g."id" = soc."giftId" 
                            
                WHERE 1=1 
                    AND s."type" = :storeType
                    AND s."deletedAt" IS NULL
                    AND sp."deletedAt" IS NULL
                    %s
                
                GROUP BY
                    s."order",
                    sp."order",
                    soc."storeId",
                    soc."giftId",
                    g."price",
                    soc."storePlaceId"
                    
                ORDER BY
                    s."order" ASC,
                    sp."order" ASC
            )
            
            SELECT
                ru."giftId",
                ru."storeId",
                ru."storePlaceId",
                ru."netCostPerStep",
                ru."realUnits",
                SUM(ABS(ru."realUnits") * ru."netCostPerStep") AS "netCost"
                
            FROM "realUnits" as ru
            
            GROUP BY
                ru."giftId",
                ru."storeId",
                ru."storePlaceId",
                ru."netCostPerStep",
                ru."realUnits"
        ';

        if($notZero){
            $sql = '
                SELECT * FROM (
                    '.$sql.'
                ) AS "ru"
                WHERE ru."realUnits" != 0
            ';
        }

        $binds[':storeType'] = StoreModel::TYPE_GIFT;
        [$where, $binds] = self::getWhereBinds($params, $binds);
        $sql = sprintf($sql, $where);

        $realUnitsProducts = DB::select($sql, $binds);
        $realUnitsProducts = Utils::objectToArray($realUnitsProducts);

        return $realUnitsProducts;
    }


    /**
     * get the condition and parameters for the search
     * @param $params
     * @param $binds
     * @return array
     */
    public static function getWhereBinds($params, $binds = []) {
        $where = '';

        if(array_key_exists('isSystem', $params) && is_bool($params['isSystem'])){
            $where .= ' AND sp."isSystem" = :isSystem ';
            $binds[':isSystem'] = $params['isSystem'];
        }

        if(boolval($params['storeId'] ?? false)){
            $where .= ' AND soc."storeId" = :storeId ';
            $binds[':storeId'] = (int) $params['storeId'];
        }


        if(boolval($params['notStoreOperationId'] ?? false)){
            $where .= ' AND soc."storeOperationId" != :notStoreOperationId ';
            $binds[':notStoreOperationId'] = $params['notStoreOperationId'];
        }

        if(boolval($params['giftIds'] ?? false)){
            $inValues = [];
            foreach ($params['giftIds'] as $k => $id){
                $key = ':'.$k.'giftIds';
                $inValues[] = $key;
                $binds[$key] = $id;
            }

            $inValues = implode(' ,', $inValues);
            $where .= ' AND soc."giftId" IN('.$inValues.')';
        }

        if(boolval($params['storePlaceIds'] ?? false)){
            $inValues = [];
            foreach ($params['storePlaceIds'] as $k => $id){
                $key = ':'.$k.'storePlaceId';
                $inValues[] = $key;
                $binds[$key] = $id;
            }

            $inValues = implode(' ,', $inValues);
            $where .= ' AND sp."id" IN('.$inValues.')';
        }

        return [
            $where,
            $binds,
        ];
    }
}